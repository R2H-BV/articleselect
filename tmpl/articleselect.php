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

if (!function_exists('getUrl')) {
	function getUrl ($title) {
		$db = Factory::getDbo();

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
			return 'We could not find the article id!!';
		}

		$url = Route::_('index.php?option=com_content&view=article&id='.$articleId);

		return '<a class="cf-article-link" href="'.$url.'" alt="'.$title.'">'.$title.'</a>';
	}
}

echo '<div class="cf-article-link-list">';
if (is_array($value))
{
	foreach ($value as $articleTitle) {
		echo getUrl($articleTitle) . '<br>';
	}
} else {
	echo getUrl($value) . '<br>';
}
echo '</div>';
