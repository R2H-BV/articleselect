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

$outputFormat = $this->params->get('outputFormat', 'links');

if (!function_exists('getUrl')) {
	function getUrl ($title, $format) {

		$db = Factory::getContainer()->get('DatabaseDriver');

		$articleId = '';
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id')));
		$query->from($db->quoteName('#__content'));
		$query->where($db->quotename('title') . ' LIKE ' . $db->quote($title));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$articleId = $db->loadResult();

		if (empty($articleId)) {
			return 'Article ID not found!';
		}

		$rawurl = 'index.php?option=com_content&view=article&id='.$articleId;
		$url = Route::_($rawurl);

		if ($format == 'links') {
			return '<a class="cf-article-link" href="'.$url.'" alt="'.$title.'">'.$title.'</a>';
		}

		if ($format == 'array') {
			return ['articleid' => $articleId, 'title' => $title, 'raw_url' => $rawurl, 'url' => $url];
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

	echo '<pre>';
	print_r($articleArray);
	echo '</pre>';
}