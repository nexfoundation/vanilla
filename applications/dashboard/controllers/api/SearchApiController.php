<?php

use Vanilla\ApiUtils;
use Garden\Web\Data;
use Vanilla\Formatting\Formats\TextFormat;

class SearchApiController extends AbstractApiController {

    use \Vanilla\Formatting\FormatCompatTrait;

    /** @var SearchModel */
    private $searchModel;

    public function __construct(SearchModel $searchModel = null) {
        if ($searchModel === null) {
            $searchModel = new SearchModel();
        }
        $this->searchModel = $searchModel;
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

        $this->searchModel->setSearchDomain($query['domain']);
        $resultCount = $this->searchModel->searchCount($query['query']);
        $resultSet = $this->searchModel->search($query['query'], $query['page']-1, $query['limit']);
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
        // die(var_dump($record));
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
