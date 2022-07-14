<?php // phpcs:ignore
declare(strict_types = 1);

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

$value = $field->value;

if ($value == '')
{
    return;
}

$outputFormat 		= $field->fieldparams->get('outputFormat', 'links');
$categories 		= $field->fieldparams->get('categories', '');
$linkswrapperclass 	= $field->fieldparams->get('linkswrapperclass', '');
$linkwrapperclass 	= $field->fieldparams->get('linkwrapperclass', '');
$linkclass 			= $field->fieldparams->get('linkclass', '');

if(strlen($linkswrapperclass)) {
    $linkswrapperclass = ' ' . $linkswrapperclass;
}

if(strlen($linkwrapperclass)) {
    $linkwrapperclass = ' ' . $linkwrapperclass;
}

if(strlen($linkclass)) {
    $linkclass = ' ' . $linkclass;
}


if (!function_exists('prepareUrls')) {
    function prepareUrls(array $ids): array
    {
        static $data = [];

        if (!count($diff = array_diff($ids, array_keys($data)))) {
            return array_map(function ($id) use ($data) {
                return $data[$id];
            }, $ids);
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        // Create a new query object.
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id', 'title', 'catid', 'images', 'alias')));
        $query->from($db->quoteName('#__content'));
        $query->where($db->quotename('id') . ' IN (' . implode(', ', array_map(function ($i) {
            return (int) $i;
        }, $diff)) . ')');

        // Reset the query using our newly populated query object.
        $db->setQuery($query);

        foreach ($db->loadRowList() as $row) {
			$row[5] = Route::_('index.php?option=com_content&view=article&id='. $row[0] .':' . $row[4] .'&catid=' . $row[2]);
            $data[$row[0]] = $row;
        }

        return array_map(function ($id) use ($data) {
            return $data[$id] ?? null;
        }, $ids);
    }
}

if (!function_exists('getUrl')) {
    function getUrl ($id, $format, $linkclasses) {

        [$row] = prepareUrls([$id]);

        if (empty($row)) {
            return 'Article ID not found!';
        }

        $title = $row[1];
        $url = $row[5];

        if ((string) $format === 'links') {
            return '<a class="cf-article-link'.$linkclasses.'" href="'.$url.'" alt="'.$title.'">'.$title.'</a>';
        }

        if ((string) $format === 'array') {
            // get custom fields values
            $object = [
                'id' => $id,
                'title' => $title,
                'raw_url' => $rawurl,
                'url' => $url,
                'link_class' => $linkclasses,
                'images' => json_decode($row[3])];

            return $object;
        }

        return $row;
    }
}

if ($outputFormat == 'links') {
    echo '<div class="cf-article-link-list'.$linkswrapperclass.'">';
    if (is_array($value))
    {
        prepareUrls($value);
        foreach ($value as $articleTitle) {
            echo '<div class="linkwrapper'.$linkwrapperclass.'">'.getUrl($articleTitle, $outputFormat, $linkclass).'</div>';
        }
    } else {
        echo '<div class="linkwrapper'.$linkwrapperclass.'">'.getUrl($value, $outputFormat, $linkclass).'</div>';
    }
    echo '</div>';
}

if ($outputFormat == 'array') {

    $articleArray = [];

    if (is_array($value))
    {
        prepareUrls($value);
        foreach ($value as $articleInfo) {
            array_push($articleArray, getUrl($articleInfo, $outputFormat, $linkwrapperclass, $linkclass));
        }
    } else {

        array_push($articleArray, getUrl($value, $outputFormat, $linkwrapperclass, $linkclass));
    }
    echo json_encode($articleArray);
}
