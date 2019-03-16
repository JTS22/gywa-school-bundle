<?php

/**
 * Table tl_exercises
 */
$GLOBALS['TL_DCA']['tl_exercises'] = array
(
	//Config
	'config' => array
    (
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        ),
    ),
	
	//List
	'list' => array
    (
        'sorting' => array
        (
            'mode' => 2,
            'fields' => array('title'),
            'flag' => 1,
            'panelLayout' => 'filter;sort,search,limit'
        ),
		'label' => array
        (
            'fields' => array('title'),
            'format' => '%s',
        ),
		'global_operations' => array
        (
            'all' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
		'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_exercises']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'copy' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_exercises']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.svg'
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_exercises']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_exercises']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            ),
        )
	),
	
	//Palettes	
	'palettes' => array
	(
		'default'	=> '{title_legend},title,category'
	),
	
	// Fields
	'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_exercises']['title'],
            'inputType' => 'text',
            'sorting' => true,
            'flag' => 1,
            'search' => true,
            'eval' => array(
                'mandatory' => true,
                'unique' => true,
                'maxlength' => 50,
                'tl_class' => 'w50'
            ),
            'sql' => "varchar(50) NOT NULL default ''"
        ),
		'category' => array(
            'label' => $GLOBALS['TL_LANG']['tl_exercises']['category'],
            'inputType' => 'select',
            'foreignKey' => 'tl_subject.title',
            'eval' => array(
				'mandatory' => true,
				'chosen' => true,
				'tl_class' => 'w50'
			),
            'relation' => array('type' => 'hasOne', 'load' => 'lazy'),
            //'sql' => "int(10) unsigned NOT NULL default '0'"
			'sql'       => "BLOB NULL"
        )
	)
);
?>