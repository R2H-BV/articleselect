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

if (!function_exists('getUrl')) {
	function getUrl ($id, $format, $linkclasses) {

		$db = Factory::getContainer()->get('DatabaseDriver');

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('title', 'catid', 'images')));
		$query->from($db->quoteName('#__content'));
		$query->where($db->quotename('id') . ' = ' . $db->quote($id));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$row = $db->loadRow();

		if (empty($row)) {
			return 'Article ID not found!';
		}

		$title = $row[0];

		$rawurl = 'index.php?option=com_content&view=article&id='. $id.'&catid=' . $row[1];
		$url = Route::_($rawurl);

		if ($format == 'links') {

			return '<a class="cf-article-link'.$linkclasses.'" href="'.$url.'" alt="'.$title.'">'.$title.'</a>';
		}

		if ($format == 'array') {
			// get custom fields values
			$object = [
				'id' => $id,
				'title' => $title,
				'raw_url' => $rawurl,
				'url' => $url,
				'link_class' => $linkclasses,
				'images' => json_decode($row[2])];

			return $object;
		}
	}
}

if ($outputFormat == 'links') {
	echo '<div class="cf-article-link-list'.$linkswrapperclass.'">';
	if (is_array($value))
	{
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
		foreach ($value as $articleInfo) {
			array_push($articleArray, getUrl($articleInfo, $outputFormat, $linkwrapperclass, $linkclass));
		}
	} else {

		array_push($articleArray, getUrl($value, $outputFormat, $linkwrapperclass, $linkclass));
	}
	echo json_encode($articleArray);
}
