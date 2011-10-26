<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

mosMainFrame::addLib('dbconfig');

class configContent_ucontent extends dbConfig {
	/**
	 * Заголовок страницы
	 */
	var $title = _C_CONTENT_USER_CONTENT;
	/**
	 * Отображать дату
	 */
	var $date = 1;
	/**
	 * Отображать количество просмотров
	 */
	var $hits = 1;
	/**
	 * Отображать раздел/категорию
	 */
	var $section = 1;
	/**
	 * Поле фильтра
	 */
	var $filter = 1;
	/**
	 * Выбор типа сортировки
	 */
	var $order_select = 1;
	/**
	 * Выпадающий список для выбора количества записей на странице
	 */
	var $display = 1;
	/**
	 * Количество записей на странице по умолчанию
	 */
	var $display_num = 10;
	/**
	 * Заголовки таблицы
	 */
	var $headings = 1;
	/**
	 * Постраничная навигация
	 */
	var $navigation = 1;

	function configContent_ucontent(&$db, $group = 'com_content', $subgroup = 'user_page') {
		$this->dbConfig($db, $group, $subgroup);
	}

	function display_config($option) { ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );

	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_USER_CONTENT?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminform">
		<tr>
			<td width="250"><?php echo _PAGE_TITLE?></td>
			<td><input class="inputbox" type="text" name="title" size="100" value="<?php echo $this->title; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_DATE?></td>
			<td><?php echo mosHTML::yesnoRadioList('date', '', $this->date?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_HIT_COUNTER?></td>
			<td><?php echo mosHTML::yesnoRadioList('hits', '', $this->hits?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_SEC_CAT?></td>
			<td><?php echo mosHTML::yesnoRadioList('section', '', $this->section?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_FILTER?></td>
			<td><?php echo mosHTML::yesnoRadioList('filter', '', $this->filter?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_ORDER?></td>
			<td><?php echo mosHTML::yesnoRadioList('order_select', '', $this->order_select?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_PAGE_LIMIT?></td>
			<td><?php echo mosHTML::yesnoRadioList('display', '', $this->display?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_DEF_PAGE_LIMIT?></td>
			<td><input class="inputbox" type="text" name="display_num" value="<?php echo $this->display_num; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_TABLE_HEADER?></td>
			<td><?php echo mosHTML::yesnoRadioList('headings', '', $this->headings?1 : 0); ?></td>
		</tr>
		<tr>
			<td><?php echo _C_CONTENT_SET_DISPLAY_PAGENAV?></td>
			<td><?php echo mosHTML::yesnoRadioList('navigation', '', $this->navigation?1 : 0); ?></td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="ucontent" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {
		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
			exit();
		}
	}
}

class configContent_sectionblog extends dbConfig {

	//Название страницы, отображаемое в заголовке браузера (тег title)
	//если не задано - берется значение параметра $header
	//еасли $header также не задан - берется название текущего раздела
	var $page_name = '';
	//показать/скрыть название сайта в title страницы (заголовке браузера)
	var $no_site_name = 1;
	//Мета-тег robots, используемый на странице:
	//int [-1,0,1,2,3]=['Не отображать', 'Index, follow', 'Index, NoFollow', 'NoIndex, Follow', 'NoIndex, NoFollow']
	var $robots = -1;
	//META-тег: Description: string
	var $meta_description = '';
	//ETA-тег keywords: string
	var $meta_keywords = '';
	//META-тег author: string
	var $meta_author = '';
	//Изображение меню
	var $menu_image = '';
	//Суффикс CSS-класса страницы
	var $pageclass_sfx = '';
	//Заголовок страницы (контентной области)
	var $header = '';
	//Показать-Скрыть заголовок страницы
	var $page_title = 1;
	//Показать-Скрыть кнопку Назад (Вернуться), возвращающую на предыдущую просмотренную страницу
	//По-умолчанию - используются глобальные настройки
	var $back_button = -1;
	//Количество главных объектов (на всю ширину). При 0 главные объекты отображаться не будут.
	var $leading = 1;
	//Количество объектов, у которых показывается вступительный (intro) текст
	var $intro = 4;
	//Сколько колонок в строке использовать при отображении вводного текста
	var $columns = 2;
	//Количество объектов, отображаемых в виде ссыло
	var $link = 4;
	//Сортировка объектов в категории
	var $orderby_pri = '';
	//Порядок, в котором будут отображаться объекты
	var $orderby_sec = '';
	//Показать/Скрыть постраничную навигацию
	var $pagination = 2;
	//Показать/Скрыть информацию о результатах разбиения на страницы ( например, 1-4 из 4 )
	var $pagination_results = 1;
	//Показывать {mosimages}
	var $image = 1;
	//Показать/Скрыть названия разделов, к которым принадлежат объекты
	var $section = 0;
	//Сделать названия разделов ссылками на страницу текущего раздела
	var $section_link = 0;
	//Тип ссылки на раздел: 'blog' / 'list'
	var $section_link_type = 'blog';
	//Показать/Скрыть названия категорий, которым принадлежат объекты
	var $category = 0;
	//Сделать названия категорий ссылками на страницу текущей категории
	var $category_link = 0;
	//Тип ссылки на категорию: 'blog' / 'table'
	var $cat_link_type = 'blog';
	//Показать/Скрыть заголовки объектов
	var $item_title = 1;
	//Сделать заголовки объектов в виде ссылок на объекты
	var $link_titles = '';
	//Показать/Скрыть ссылку [Подробнее...]
	//По-умолчанию - используются глобальные настройки
	var $readmore = '';
	//Показать/Скрыть возможность оценки объектов
	var $rating = '';
	//Показать/Скрыть имена авторов объектов
	var $author = '';
	//Тип отображения имен авторов
	var $author_name = '';
	//Показать/Скрыть дату создания объекта
	var $createdate = '';
	//оказать/Скрыть дату изменения объекта
	var $modifydate = '';
	//Показать/Скрыть кнопку печати объекта
	var $print = '';
	//Показать/Скрыть кнопку отправки объекта на e-mail
	var $email = '';
	//Показать/Скрыть неопубликованные объекты для группы пользователей `Publisher` и выше
	var $unpublished = 0;
	//Группировка по категориям
	var $group_cat = 0;
	//Количество записей в группе
	var $groupcat_limit = 5;

	//Показать/Скрыть описание раздела
	var $description = 0;
	//Показать/Скрыть изображение описания раздела
	var $description_image = 0;

	//Показать/Скрыть вводный текст
	var $view_introtext = 1;
	//Лимит слов для интротекста. Если текст не нуждается в обрезке - оставьте поле пустым
	var $introtext_limit = '';
	//Только интротекст
	var $intro_only = 1;

	//Показать/Скрыть тэги материалов
	var $view_tags = 1;

	function configContent_sectionblog(&$db, $group = 'com_content', $subgroup = 'section_blog') {
		$this->dbConfig($db, $group, $subgroup);
	}

	function display_config($option) {
		$mainframe = mosMainFrame::getInstance();

		$params = $this->prepare_for_xml_render();
		$params = new mosParameters($params, $mainframe->getPath('menu_xml', 'content_blog_section'), 'menu'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_SECTION_BLOG?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
			<?php echo $params->render(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="sectionblog" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {

		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				$_REQUEST[$k] = $v;
			}

		}

		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
			exit();
		}
	}
}

class configContent_categoryblog extends dbConfig {

	//Название страницы, отображаемое в заголовке браузера (тег title)
	//если не задано - берется значение параметра $header
	//еасли $header также не задан - берется название текущей категории
	var $page_name = '';
	//показать/скрыть название сайта в title страницы (заголовке браузера)
	var $no_site_name = 1;
	//Мета-тег robots, используемый на странице:
	//int [-1,0,1,2,3]=['Не отображать', 'Index, follow', 'Index, NoFollow', 'NoIndex, Follow', 'NoIndex, NoFollow']
	var $robots = -1;
	//META-тег: Description: string
	var $meta_description = '';
	//ETA-тег keywords: string
	var $meta_keywords = '';
	//META-тег author: string
	var $meta_author = '';
	//Изображение меню
	var $menu_image = '';
	//Суффикс CSS-класса страницы
	var $pageclass_sfx = '';
	//Заголовок страницы (контентной области)
	var $header = '';
	//Показать-Скрыть заголовок страницы
	var $page_title = 1;
	//Показать-Скрыть кнопку Назад (Вернуться), возвращающую на предыдущую просмотренную страницу
	var $back_button = '-1';
	//Количество главных объектов (на всю ширину). При 0 главные объекты отображаться не будут.
	var $leading = 1;
	//Количество объектов, у которых показывается вступительный (intro) текст
	var $intro = 4;
	//Сколько колонок в строке использовать при отображении вводного текста
	var $columns = 2;
	//Количество объектов, отображаемых в виде ссыло
	var $link = 4;
	//Сортировка объектов в категории
	var $orderby_pri = '';
	//Порядок, в котором будут отображаться объекты
	var $orderby_sec = '';
	//Показать/Скрыть постраничную навигацию
	var $pagination = 2;
	//Показать/Скрыть информацию о результатах разбиения на страницы ( например, 1-4 из 4 )
	var $pagination_results = 1;
	//Показывать {mosimages}
	var $image = 1;
	//Показать/Скрыть названия разделов, к которым принадлежат объекты
	var $section = 0;
	//Сделать названия разделов ссылками на страницу текущего раздела
	var $section_link = 0;
	//Тип ссылки на раздел: 'blog' / 'list'
	var $section_link_type = 'blog';
	//Показать/Скрыть названия категорий, которым принадлежат объекты
	var $category = 0;
	//Сделать названия категорий ссылками на страницу текущей категории
	var $category_link = 0;
	//Тип ссылки на категорию: 'blog' / 'table'
	var $cat_link_type = 'blog';
	//Показать/Скрыть заголовки объектов
	var $item_title = 1;
	//Сделать заголовки объектов в виде ссылок на объекты
	var $link_titles = '';
	//Показать/Скрыть ссылку [Подробнее...]
	var $readmore = '';
	//Показать/Скрыть возможность оценки объектов
	var $rating = '';
	//Показать/Скрыть имена авторов объектов
	var $author = '';
	//Тип отображения имен авторов
	var $author_name = '';
	//Показать/Скрыть дату создания объекта
	var $createdate = '';
	//оказать/Скрыть дату изменения объекта
	var $modifydate = '';
	//Показать/Скрыть кнопку печати объекта
	var $print = '';
	//Показать/Скрыть кнопку отправки объекта на e-mail
	var $email = '';
	//Показать/Скрыть неопубликованные объекты для группы пользователей `Publisher` и выше
	var $unpublished = 0;
	//Показать/Скрыть описание раздела
	var $description = 0;
	//Показать/Скрыть изображение описания раздела
	var $description_image = 0;
	//Показать/Скрыть вводный текст
	var $view_introtext = 1;
	//Лимит слов для интротекста. Если текст не нуждается в обрезке - оставьте поле пустым
	//TODO: сделать настройку "Обрезка по символам"
	//TODO: сделать настройку "Сделать первое изображение из текста картинкой к записи" (+ чтобы этот элемент стал доступен в шаблоне)
	var $introtext_limit = '';
	//Только интротекст
	var $intro_only = 1;

	//Показать/Скрыть тэги материалов
	var $view_tags = 1;
	var $_params = null;

	function configContent_categoryblog(&$db, $group = 'com_content', $subgroup = 'category_blog') {
		$this->dbConfig($db, $group, $subgroup);
		$this->_params = $this;
	}

	function display_config($option) {
		$mainframe = mosMainFrame::getInstance();

		$params = $this->prepare_for_xml_render();
		$params = new mosParameters($params, $mainframe->getPath('menu_xml', 'content_blog_category'), 'menu'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_CATEGORY_BLOG?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
			<?php echo $params->render(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="categoryblog" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {

		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				$_REQUEST[$k] = $v;
			}

		}

		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
}

class configContent_sectionarchive extends dbConfig {

	//Название страницы, отображаемое в заголовке браузера (тег title)
	var $page_name = '';
	//показать/скрыть название сайта в title страницы (заголовке браузера)
	var $no_site_name = 1;
	//Мета-тег robots, используемый на странице:
	//int [-1,0,1,2,3]=['Не отображать', 'Index, follow', 'Index, NoFollow', 'NoIndex, Follow', 'NoIndex, NoFollow']
	var $robots = -1;
	//META-тег: Description: string
	var $meta_description = '';
	//ETA-тег keywords: string
	var $meta_keywords = '';
	//META-тег author: string
	var $meta_author = '';
	//Изображение меню
	var $menu_image = '';
	//Суффикс CSS-класса страницы
	var $pageclass_sfx = '';
	//Заголовок страницы (контентной области)
	var $header = '';
	//Показать-Скрыть заголовок страницы
	var $page_title = 1;
	//Показать-Скрыть кнопку Назад (Вернуться), возвращающую на предыдущую просмотренную страницу
	var $back_button = '';
	//Количество главных объектов (на всю ширину). При 0 главные объекты отображаться не будут.
	var $leading = 1;
	//Количество объектов, у которых показывается вступительный (intro) текст
	var $intro = 4;
	//Сколько колонок в строке использовать при отображении вводного текста
	var $columns = 2;
	//Количество объектов, отображаемых в виде ссыло
	var $link = 4;
	//Сортировка объектов в категории
	var $orderby_pri = '';
	//Порядок, в котором будут отображаться объекты
	var $orderby_sec = '';
	//Показать/Скрыть постраничную навигацию
	var $pagination = 2;
	//Показать/Скрыть информацию о результатах разбиения на страницы ( например, 1-4 из 4 )
	var $pagination_results = 1;
	//Показывать {mosimages}
	var $image = 1;
	//Показать/Скрыть названия разделов, к которым принадлежат объекты
	var $section = 0;
	//Сделать названия разделов ссылками на страницу текущего раздела
	var $section_link = 0;
	//Тип ссылки на раздел: 'blog' / 'list'
	var $section_link_type = 'blog';
	//Показать/Скрыть названия категорий, которым принадлежат объекты
	var $category = 0;
	//Сделать названия категорий ссылками на страницу текущей категории
	var $category_link = 0;
	//Тип ссылки на категорию: 'blog' / 'table'
	var $cat_link_type = 'blog';
	//Показать/Скрыть заголовки объектов
	var $item_title = 1;
	//Сделать заголовки объектов в виде ссылок на объекты
	var $link_titles = '';
	//Показать/Скрыть ссылку [Подробнее...]
	var $readmore = '';
	//Показать/Скрыть возможность оценки объектов
	var $rating = '';
	//Показать/Скрыть имена авторов объектов
	var $author = '';
	//Тип отображения имен авторов
	var $author_name = '';
	//Показать/Скрыть дату создания объекта
	var $createdate = '';
	//оказать/Скрыть дату изменения объекта
	var $modifydate = '';
	//Показать/Скрыть кнопку печати объекта
	var $print = '';
	//Показать/Скрыть кнопку отправки объекта на e-mail
	var $email = '';
	//Показать/Скрыть неопубликованные объекты для группы пользователей `Publisher` и выше
	var $unpublished = 0;
	//Группировка по категориям
	var $group_cat = 0;
	//Количество записей в группе
	var $groupcat_limit = 5;

	//Показать/Скрыть описание раздела
	var $description = 0;
	//Показать/Скрыть изображение описания раздела
	var $description_image = 0;

	//Показать/Скрыть вводный текст
	var $view_introtext = 1;
	//Лимит слов для интротекста. Если текст не нуждается в обрезке - оставьте поле пустым
	var $introtext_limit = '';
	//Только интротекст
	var $intro_only = 1;

	function configContent_sectionarchive(&$db, $group = 'com_content', $subgroup = 'section_archive') {
		$this->dbConfig($db, $group, $subgroup);
	}

	function display_config($option) {
		global $mainframe;
		$params = $this->prepare_for_xml_render();
		$params = new mosParameters($params, $mainframe->getPath('menu_xml', 'content_archive_section'), 'menu'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );

	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_SECTION_ARHIVE?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
			<?php echo $params->render(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="sectionarchive" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {

		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				$_REQUEST[$k] = $v;
			}

		}

		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
			exit();
		}
	}
}

class configContent_categoryarchive extends dbConfig {

	//Название страницы, отображаемое в заголовке браузера (тег title)
	var $page_name = '';
	//показать/скрыть название сайта в title страницы (заголовке браузера)
	var $no_site_name = 1;
	//Мета-тег robots, используемый на странице:
	//int [-1,0,1,2,3]=['Не отображать', 'Index, follow', 'Index, NoFollow', 'NoIndex, Follow', 'NoIndex, NoFollow']
	var $robots = -1;
	//META-тег: Description: string
	var $meta_description = '';
	//ETA-тег keywords: string
	var $meta_keywords = '';
	//META-тег author: string
	var $meta_author = '';
	//Изображение меню
	var $menu_image = '';
	//Суффикс CSS-класса страницы
	var $pageclass_sfx = '';
	//Заголовок страницы (контентной области)
	var $header = '';
	//Показать-Скрыть заголовок страницы
	var $page_title = 1;
	//Показать-Скрыть кнопку Назад (Вернуться), возвращающую на предыдущую просмотренную страницу
	var $back_button = '';
	//Количество главных объектов (на всю ширину). При 0 главные объекты отображаться не будут.
	var $leading = 1;
	//Количество объектов, у которых показывается вступительный (intro) текст
	var $intro = 4;
	//Сколько колонок в строке использовать при отображении вводного текста
	var $columns = 2;
	//Количество объектов, отображаемых в виде ссыло
	var $link = 4;
	//Сортировка объектов в категории
	var $orderby_pri = '';
	//Порядок, в котором будут отображаться объекты
	var $orderby_sec = '';
	//Показать/Скрыть постраничную навигацию
	var $pagination = 2;
	//Показать/Скрыть информацию о результатах разбиения на страницы ( например, 1-4 из 4 )
	var $pagination_results = 1;
	//Показывать {mosimages}
	var $image = 1;
	//Показать/Скрыть названия разделов, к которым принадлежат объекты
	var $section = 0;
	//Сделать названия разделов ссылками на страницу текущего раздела
	var $section_link = 0;
	//Тип ссылки на раздел: 'blog' / 'list'
	var $section_link_type = 'blog';
	//Показать/Скрыть названия категорий, которым принадлежат объекты
	var $category = 0;
	//Сделать названия категорий ссылками на страницу текущей категории
	var $category_link = 0;
	//Тип ссылки на категорию: 'blog' / 'table'
	var $cat_link_type = 'blog';
	//Показать/Скрыть заголовки объектов
	var $item_title = 1;
	//Сделать заголовки объектов в виде ссылок на объекты
	var $link_titles = '';
	//Показать/Скрыть ссылку [Подробнее...]
	var $readmore = '';
	//Показать/Скрыть возможность оценки объектов
	var $rating = '';
	//Показать/Скрыть имена авторов объектов
	var $author = '';
	//Тип отображения имен авторов
	var $author_name = '';
	//Показать/Скрыть дату создания объекта
	var $createdate = '';
	//оказать/Скрыть дату изменения объекта
	var $modifydate = '';
	//Показать/Скрыть кнопку печати объекта
	var $print = '';
	//Показать/Скрыть кнопку отправки объекта на e-mail
	var $email = '';
	//Показать/Скрыть неопубликованные объекты для группы пользователей `Publisher` и выше
	var $unpublished = 0;
	//Показать/Скрыть описание раздела
	var $description = 0;
	//Показать/Скрыть изображение описания раздела
	var $description_image = 0;
	//Показать/Скрыть вводный текст
	var $view_introtext = 1;
	//Лимит слов для интротекста. Если текст не нуждается в обрезке - оставьте поле пустым
	//TODO: попробовать организовать обрезку по словам без очистки тэгов
	//TODO: сделать настройку "Очистка текста"
	//TODO: сделать настройку "Обрезка по символам"
	//TODO: сделать настройку "Сделать первое изображение из текста картинкой к записи" (+ чтобы этот элемент стал доступен в шаблоне)
	var $introtext_limit = '';
	//Только интротекст
	var $intro_only = 1;

	function configContent_categoryarchive(&$db, $group = 'com_content', $subgroup = 'category_archive') {
		$this->dbConfig($db, $group, $subgroup);
	}

	function display_config($option) {
		global $mainframe;
		$params = $this->prepare_for_xml_render();
		$params = new mosParameters($params, $mainframe->getPath('menu_xml', 'content_archive_category'), 'menu'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );

	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_CATEGORY_ARHIVE?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
			<?php echo $params->render(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="categoryarchive" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {

		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				$_REQUEST[$k] = $v;
			}

		}

		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
}

class configContent_categorytable extends dbConfig {

	//Название страницы, отображаемое в заголовке браузера (тег title)
	var $page_name = '';
	//показать/скрыть название сайта в title страницы (заголовке браузера)
	var $no_site_name = 1;
	//Мета-тег robots, используемый на странице:
	//int [-1,0,1,2,3]=['Не отображать', 'Index, follow', 'Index, NoFollow', 'NoIndex, Follow', 'NoIndex, NoFollow']
	var $robots = -1;
	//META-тег: Description: string
	var $meta_description = '';
	//ETA-тег keywords: string
	var $meta_keywords = '';
	//META-тег author: string
	var $meta_author = '';
	//Изображение меню
	var $menu_image = '';
	//Суффикс CSS-класса страницы
	var $pageclass_sfx = '';
	//Заголовок страницы (контентной области)
	var $header = '';
	//Показать-Скрыть заголовок страницы
	var $page_title = 1;
	//Показать-Скрыть кнопку Назад (Вернуться), возвращающую на предыдущую просмотренную страницу
	var $back_button = -1;
	//Показать/Скрыть описание категории
	var $description_cat = 1;
	//Показать/Скрыть изображение в описании категории
	var $description_cat_image = 1;
	//Порядок сортиров
	var $orderby = '';

	//Формат отображаемой даты. Для форматирования используется функция PHP - strftime.
	//Если поле оставлено пустым, то будет использоваться формат из языкового файла
	var $date_format = '';

	//Показать/Скрыть в таблице столбец Дата
	var $date = '';
	//Показать/Скрыть столбец Автор
	var $author = '';
	//Механизм отображения имен авторов
	var $author_name = 0;
	//Показать/Скрыть столбец Просмотров
	var $hits = 0;
	//Показать/Скрыть заголовки таблиц
	var $headings = 1;
	//Показать/Скрыть строку навигации
	var $navigation = 1;
	//Показать/Скрыть раскрывающийся список с выбором способа сортировки
	var $order_select = 1;
	//Показать/Скрыть раскрывающийся список с выбором количества отображаемых объектов на странице
	var $display = 1;
	//Количество отображаемых объектов по умолчанию
	var $display_num = 50;
	//Показать/Скрыть возможность фильтрации
	var $filter = 1;
	//Какое поле использовать для фильтрации
	var $filter_type = 'title';
	//Показать/Скрыть список других категорий
	var $other_cat = 1;
	//Показать/Скрыть пустые (без объектов) категории
	var $empty_cat = 0;
	//Показать/Скрыть число объектов каждой категории
	var $cat_items = 1;
	//Показать/Скрыть описание категории, которое располагается ниже названия категории
	var $cat_description = 1;
	//Показать/Скрыть неопубликованные объекты для группы пользователей `Publisher` и выше
	var $unpublished = 1;

	//Показать/Скрыть вводный текст
	//TODO: реализовать
	var $view_introtext = 1;
	//Лимит слов для интротекста. Если текст не нуждается в обрезке - оставьте поле пустым
	//TODO: попробовать организовать обрезку по словам без очистки тэгов
	//TODO: сделать настройку "Очистка текста"
	//TODO: сделать настройку "Обрезка по символам"
	//TODO: сделать настройку "Сделать первое изображение из текста картинкой к записи" (+ чтобы этот элемент стал доступен в шаблоне)
	var $introtext_limit = '';

	function configContent_categorytable(&$db, $group = 'com_content', $subgroup = 'category_table') {
		$this->dbConfig($db, $group, $subgroup);
	}

	function display_config($option) {
		global $mainframe;
		$params = $this->prepare_for_xml_render();
		$params = new mosParameters($params, $mainframe->getPath('menu_xml', 'content_category'), 'menu'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );

	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_CATEGORY_TABLE?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
			<?php echo $params->render(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="categorytable" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {

		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				$_REQUEST[$k] = $v;
			}
		}

		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
}

class configContent_sectionlist extends dbConfig {

	//Название страницы, отображаемое в заголовке браузера (тег title)
	//если не задано - берется значение параметра $header
	//если $header также не задан - берется название текущего раздела
	var $page_name = '';
	//показать/скрыть название сайта в title страницы (заголовке браузера)
	var $no_site_name = 1;
	//Мета-тег robots, используемый на странице:
	//int [-1,0,1,2,3]=['Не отображать', 'Index, follow', 'Index, NoFollow', 'NoIndex, Follow', 'NoIndex, NoFollow']
	var $robots = -1;
	//META-тег: Description: string
	var $meta_description = '';
	//ETA-тег keywords: string
	var $meta_keywords = '';
	//META-тег author: string
	var $meta_author = '';
	//Изображение меню
	var $menu_image = '';
	//Суффикс CSS-класса страницы
	var $pageclass_sfx = '';
	//Заголовок страницы (контентной области)
	//если  не задан - берется название текущего раздела
	var $header = '';
	//Показать-Скрыть заголовок страницы
	var $page_title = 1;
	//Показать-Скрыть кнопку Назад (Вернуться), возвращающую на предыдущую просмотренную страницу
	var $back_button = -1;
	//Показать/Скрыть описание раздела
	var $description_sec = 1;
	//Показать/Скрыть изображение в описании раздела
	var $description_sec_image = 1;
	//Порядок сортировки
	var $orderby = '';
	//Тип ссылки на категорию: 'blog' / 'table'
	var $cat_link_type = 'table';
	//Показать/Скрыть пустые (без объектов) категории при просмотре раздела
	var $empty_cat_section = 0;
	//Показать/Скрыть описание каждой категории в списке
	var $description = 1;
	//Показать/Скрыть изображение в описании каждой категории в списке
	var $description_image = 1;

	//Показать/Скрыть число объектов каждой категории
	var $cat_items = 1;
	//Показать/Скрыть описание категории, которое располагается ниже названия категории
	var $cat_description = 1;

	function configContent_sectionlist(&$db, $group = 'com_content', $subgroup = 'section_list') {
		$this->dbConfig($db, $group, $subgroup);
	}

	function display_config($option) {
		global $mainframe;
		$params = $this->prepare_for_xml_render();
		$params = new mosParameters($params, $mainframe->getPath('menu_xml', 'content_section'), 'menu'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );

	}
</script>
<table class="adminheading"><tbody>
		<tr><th class="config">
						<?php echo _C_CONTENT_SET_DEF_CONTENT_SETTINGS?><br />
				<small><?php echo _C_CONTENT_SET_SECTION_TABLE?></small>
			</th></tr>
	</tbody></table>
<form action="index2.php" method="post" name="adminForm">
			<?php echo $params->render(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="act" value="sectionlist" />
	<input type="hidden" name="task" value="save_config" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php }

	function save_config() {

		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				$_REQUEST[$k] = $v;
			}
		}

		if(!$this->bindConfig($_REQUEST)) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$this->storeConfig()) {
			echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
}