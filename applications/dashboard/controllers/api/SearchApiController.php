<?php

use Vanilla\ApiUtils;
use Garden\Web\Data;
use Vanilla\Formatting\Formats\TextFormat;

class SearchApiController extends AbstractApiController {

    use \Vanilla\Formatting\FormatCompatTrait;
    use \Vanilla\Formatting\FormatFieldTrait;
    use \Garden\SphinxTrait;

    /** @var SearchModel */
    private $searchModel;

    public function __construct(SearchModel $searchModel = null) {
        if ($searchModel === null) {
            $searchModel = new SearchModel();
        }
        $this->searchModel = $searchModel;
    }

    private function getSphinxIndex(string $domain = 'all_content') {
        $dbName = c('Database.Name');
        $commentIndex = "{$dbName}_Comment_Delta {$dbName}_Comment";
        $discussionIndex = "{$dbName}_Discussion_Delta {$dbName}_Discussion";
        switch ($domain) {
            case 'all_content':
                return join(" ", [$commentIndex, $discussionIndex]);
            case 'discussions':
                return $discussionIndex;
            default:
                return join(" ", [$commentIndex, $discussionIndex]);
        }
    }

    private function sphinxSearch(string $query, $offset = 0, $limit = 20, $domain = 'all_content') {
        $results = [];
        $client = $this->sphinxClient();
        $client->setLimits($offset, $limit);
        $searchResult = $client->query($query, $this->getSphinxIndex($domain));
        if (!$searchResult || $searchResult['total'] <= 0) {
            return $results;
        }
        $discussions = array_map(
            function ($match) {
                var_dump($match);
                return $match['attrs']['discussionid'];
            },
            array_values($searchResult['matches'])
        );
        $results = $this->searchModel->getDiscussionsIn($discussions);
        $results = array_map(
            function ($result) {
                $result['Relevance'] = 0.0;
                $this->formatField($result, 'Summary', $result['Format'] ?? 'Html');
                $this->formatField($result, 'Body', $result['Format'] ?? 'Html');
               return $result;
            },
            $results
        );
        return $results;

    }

    public function index(array $query) {
        $this->permission();

        $in =  $this->schema([
            'domain:s' => [
                'description' => 'all or discussions',
                'enum' => ['all_content', 'discussions'],
                'default'=> 'all_content'
            ],
            'query:s' => [
                'description' => 'query string',
                'default'=> ''
            ],
            'page:i?' => [
                'description' => 'Page number.',
                'default' => 1,
                'minimum' => 1,
                'maximum' => 100
            ],
            'limit:i?' => [
                'description' => 'Desired number of items per page.',
                'default' => 10,
                'minimum' => 1,
                'maximum' => 100
            ]
            ], 'in');

        $query = $in->validate($query);
        if ($query['query'] == '') {
            return new Data([], [], ['link' => '']);
        }

        if (!$this->sphinxClient()->status()) {
            $this->searchModel->setSearchDomain($query['domain']);
            $resultCount = $this->searchModel->searchCount($query['query']);
            $resultSet = $this->searchModel->search(
                $query['query'],
                ($query['page'] - 1) * $query['limit'],
                $query['limit']
            );
        } else {
            $resultSet = $this->sphinxSearch(
                $query['query'],
                ($query['page'] - 1) * $query['limit'],
                $query['limit'],
                $query['domain']
            );
            $resultCount = count($resultSet);
        }

        foreach ($resultSet as &$row) {
            $row = $this->normalizeOutput($row);
        }
        $paging = ApiUtils::numberedPagerInfo($resultCount, '/api/v2/search', $query, $in);
        return new Data(
            $resultSet, [],
            [
                'link' => $this->pagerLink($paging),
                'x-app-page-result-count' => $resultCount,
                'x-app-page-current' => 1,
                'x-app-page-limit' => 10
            ]);
    }

    private function pagerLink($paging) {
        if (
            !isset($paging['urlFormat']) ||
            !isset($paging['page']) ||
            !isset($paging['pageCount'])
        ) {
            return "";
        }
        $urlFormat = $paging['urlFormat'];
        $page = $paging['page'];
        $pageCount = $paging['pageCount'];
        $links = [];
        if ($page > 1) {
            $links[] = '<'.sprintf($urlFormat, $page-1).'>; rel="prev"';
        }
        if ($page < $pageCount) {
            $links[] = '<'.sprintf($urlFormat, $page+1).'>; rel="next"';
        }
        return implode(',', $links);
    }

    private function normalizeOutput(array $record) {
        $normalized = [];
        $normalized['name'] = $record['Title'];
        $normalized['url'] = $record['Url'];
        $normalized['body'] = Gdn::formatService()->renderPlainText($record['Summary'], "html");
        $normalized['recordID'] = $record['PrimaryID'];
        $normalized['type'] = $normalized['recordType'] = strtolower($record['RecordType']);
        $normalized['breadcrumbs'] = [];
        $normalized['dateInserted'] = $record['DateInserted'];
        $normalized['score'] = $record['Relevance'];
        return $normalized;
    }
}
