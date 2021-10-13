<?php // phpcs:ignore
declare(strict_types = 1);

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

$value = $field->value;



if ($value == '')
{
	return;
}

$outputFormat = $field->fieldparams->get('outputFormat', 'links');
$categories = $field->fieldparams->get('categories', '');

if (!function_exists('getUrl')) {
	function getUrl ($id, $format) {

		$db = Factory::getContainer()->get('DatabaseDriver');

		$articleId = '';
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
			return '<a class="cf-article-link" href="'.$url.'" alt="'.$title.'">'.$title.'</a>';
		}

		if ($format == 'array') {
			// get custom fields values
			$object = ['id' => $id, 'title' => $title, 'raw_url' => $rawurl, 'url' => $url, 'images' => json_decode($row[2])];

			return $object;
		}

		if ($format == 'buttons') {
			return '<a class="btn btn-success me-2 rounded-pill cf-article-btn-link" href="'.$url.'" alt="'.$title.'">'.$title.'</a>';
		}
	}
}

if ($outputFormat == 'links') {
	echo '<div class="cf-article-link-list">';
	if (is_array($value))
	{
		foreach ($value as $articleTitle) {
			echo getUrl($articleTitle, $outputFormat) . '<br>';
		}
	} else {
		echo getUrl($value, $outputFormat) . '<br>';
	}
	echo '</div>';
}

if ($outputFormat == 'buttons') {
	echo '<div class="cf-article-button-list">';
	if (is_array($value))
	{
		foreach ($value as $articleTitle) {
			echo getUrl($articleTitle, $outputFormat);
		}
	} else {
		echo getUrl($value, $outputFormat);
	}
	echo '</div>';
}

if ($outputFormat == 'array') {

	$articleArray = [];

	if (is_array($value))
	{
		foreach ($value as $articleInfo) {
			array_push($articleArray, getUrl($articleInfo, $outputFormat));
		}
	} else {

		array_push($articleArray, getUrl($value, $outputFormat));
	}

	echo json_encode($articleArray);

}