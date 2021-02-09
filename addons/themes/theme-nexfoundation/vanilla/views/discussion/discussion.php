<?php
/**
 * @copyright 2009-2019 Vanilla Forums Inc.
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
 */

if (!defined('APPLICATION')) {
    exit();
}
use Vanilla\Utility\HtmlUtils;
$UserPhotoFirst = c('Vanilla.Comment.UserPhotoFirst', true);

$Discussion = $this->data('Discussion');

//all data
// print_r($Discussion);


$Author = Gdn::userModel()->getID($Discussion->InsertUserID); // userBuilder($Discussion, 'Insert');

// $bookmarkCount =  print_r($Discussion,true);

// Prep event args.
$CssClass = cssClass($Discussion, false);
$this->EventArguments['Discussion'] = &$Discussion;
$this->EventArguments['Author'] = &$Author;
$this->EventArguments['CssClass'] = &$CssClass;



// DEPRECATED ARGUMENTS (as of 2.1)
$this->EventArguments['Object'] = &$Discussion;
$this->EventArguments['Type'] = 'Discussion';
$Attr =json_decode ($Discussion->DataAttribute);
$bookmarks =$Attr->bookmarks;

// $Discussion->CountComments = &$CountComments;

$CountComments = $Discussion->CountComments;


//tag ,bookmarks
// $Discussion->DataAttribute




// Discussion template event
$this->fireEvent('BeforeDiscussionDisplay');

?>
<div id="<?php echo 'Discussion_'.$Discussion->DiscussionID; ?>" class="<?php echo $CssClass; ?>">
    <div class="Discussion">
        <?php $this->fireEvent('BeforeDiscussionBody'); ?>
        <div class="Item-BodyWrap">
            <div class="Item-Body">
                <div class="Message userContent">
                    <?php
                    // echo("99999內文");
                    echo formatBody($Discussion);
                    ?>
                </div>
                <?php
                $this->fireEvent('AfterDiscussionBody');
                writeReactions($Discussion);
                if (val('Attachments', $Discussion)) {
                    writeAttachments($Discussion->Attachments);
                }
                ?>
            </div>
            <div class="Item-Header DiscussionHeader">
        <?php

$this->fireEvent('AfterDiscussionMeta'); // DEPRECATED

        ?>
                <span class="MItem MCount ViewCount">
                    <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.119 0.25C12.6369 0.25 13.1335 0.478273 13.4996 0.884602C13.8658 1.29093 14.0715 1.84203 14.0715 2.41667V19.75L7.23786 16.5L0.404221 19.75V2.41667C0.404221 1.84203 0.609927 1.29093 0.976086 0.884602C1.34224 0.478273 1.83886 0.25 2.35669 0.25H12.119ZM6.26163 4.58333V6.75H4.30916V8.91667H6.26163V11.0833H8.2141V8.91667H10.1666V6.75H8.2141V4.58333H6.26163Z" fill="#FF3559"/>
                    </svg>
                    <?php
                    echo ($bookmarks);
                    echo (t('bookmarked'));
                    ?>
                </span>
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.55914 20C7.29393 20 7.03957 19.8946 6.85204 19.7071C6.6645 19.5196 6.55914 19.2652 6.55914 19C6.55914 17.3431 5.216 16 3.55914 16H2.55914C2.02871 16 1.52 15.7893 1.14493 15.4142C0.769857 15.0391 0.559143 14.5304 0.559143 14V2C0.559143 1.46957 0.769857 0.960859 1.14493 0.585786C1.52 0.210714 2.02871 0 2.55914 0H18.5591C19.0896 0 19.5983 0.210714 19.9734 0.585786C20.3484 0.960859 20.5591 1.46957 20.5591 2V14C20.5591 14.5304 20.3484 15.0391 19.9734 15.4142C19.5983 15.7893 19.0896 16 18.5591 16H17.6988C14.3444 16 11.1279 17.3349 8.75914 19.71C8.55914 19.9 8.30914 20 8.05914 20H7.55914ZM14.5591 9C15.1114 9 15.5591 8.55229 15.5591 8C15.5591 7.44772 15.1114 7 14.5591 7C14.0069 7 13.5591 7.44772 13.5591 8C13.5591 8.55229 14.0069 9 14.5591 9ZM10.5591 9C11.1114 9 11.5591 8.55229 11.5591 8C11.5591 7.44772 11.1114 7 10.5591 7C10.0069 7 9.55914 7.44772 9.55914 8C9.55914 8.55229 10.0069 9 10.5591 9ZM6.55914 9C7.11143 9 7.55914 8.55229 7.55914 8C7.55914 7.44772 7.11143 7 6.55914 7C6.00686 7 5.55914 7.44772 5.55914 8C5.55914 8.55229 6.00686 9 6.55914 9Z" fill="#FF3559"/>
                </svg>

                <span class="MItem MCount CommentCount">
                    <?php
                    printf(pluralTranslate($Discussion->CountCommentss, '%s comment html', '%s comments html', t('%s comment') , t('%s comments')) , bigPlural($Discussion->CountComments, '%s comment'));
                    ?>
                </span>
            <div class="AuthorWrap">
            <span class="Author">
                <?php
                if ($UserPhotoFirst) {
                    echo userPhoto($Author);
                    echo userAnchor($Author, 'Username');
                } else {
                    echo userAnchor($Author, 'Username');
                    echo userPhoto($Author);
                }
                echo formatMeAction($Discussion);
?>
            </span>
            <span class="AuthorInfo">
                <?php
                echo wrapIf(htmlspecialchars(val('Title', $Author)), 'span', ['class' => 'MItem AuthorTitle']);
                echo wrapIf(htmlspecialchars(val('Location', $Author)), 'span', ['class' => 'MItem AuthorLocation']);
                $this->fireEvent('AuthorInfo');
                ?>
            </span>
            </div>
            <div class="Meta DiscussionMeta">
            <!-- 原本文章的時間 -->
            <span class="MItem DateCreated">
                <?php
                // echo anchor(Gdn_Format::date($Discussion->DateInserted, 'html'), $Discussion->Url, 'Permalink', ['rel' => 'nofollow']);
                ?>
            </span>
                <?php
                // echo dateUpdated($Discussion, ['<span class="MItem">', '</span>']);
                ?>
                <?php
                // Include source if one was set
                if ($Source = val('Source', $Discussion)) {
                    echo ' '.wrap(sprintf(t('via %s'), t($Source.' Source', $Source)), 'span', ['class' => 'MItem MItem-Source']).' ';
                }
                // Category
                if (c('Vanilla.Categories.Use')) {
                    $accessibleLabel = HtmlUtils::accessibleLabel('Category: "%s"', [$this->data('Discussion.Category')]);
                    echo ' <span class="MItem Category">';
                    echo ' '.t('in').' ';
                    echo anchor(htmlspecialchars($this->data('Discussion.Category')), categoryUrl($this->data('Discussion.CategoryUrlCode')), ["aria-label" => $accessibleLabel]);
                    echo '</span> ';
                }

                // Include IP Address if we have permission
                if ($Session->checkPermission('Garden.PersonalInfo.View')) {
                    echo wrap(ipAnchor($Discussion->InsertIPAddress), 'span', ['class' => 'MItem IPAddress']);
                }

                $this->fireEvent('DiscussionInfo');
                $this->fireEvent('AfterDiscussionMeta'); // DEPRECATED
                ?>
            </div>
        </div>
        </div>
    </div>
</div>
