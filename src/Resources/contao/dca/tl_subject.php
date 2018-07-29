<?php

/**
 * Table tl_subject
 */
$GLOBALS['TL_DCA']['tl_subject'] = array
(

    // Config
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
    // List
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
            'fields' => array('title', 'abbreviation'),
            'format' => '%s (%s)',
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
                'label' => &$GLOBALS['TL_LANG']['tl_subject']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'copy' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_subject']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.svg'
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_subject']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_subject']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
                'attributes' => 'style="margin-right:3px"'
            ),
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,cssClass,abbreviation,category,referencePage'
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
            'label' => &$GLOBALS['TL_LANG']['tl_subject']['title'],
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
        'cssClass' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_subject']['cssClass'],
            'inputType' => 'text',
            'eval' => array('maxlenght' => 64, 'tl_class' => 'w50'),
            'sql' => "varchar(64) NOT NULL default ''"
        ),
        'abbreviation' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_subject']['abbreviation'],
            'inputType' => 'text',
            'search' => true,
            'eval' => array('maxlenght' => 6, 'tl_class' => 'w50', 'unique' => true),
            'save_callback' => array(
                array('gywaschoolbundle.subjectManager', 'onSubjectAbbrSaved')
            ),
            'sql' => "varchar(6) NOT NULL default ''"
        ),
        'referencePage' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_subject']['referencePage'],
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => array('fieldType' => 'radio', 'tl_class' => 'clr'),
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => array('type' => 'hasOne', 'load' => 'lazy')
        ),
        'category' => array(
            'label' => $GLOBALS['TL_LANG']['tl_subject']['category'],
            'inputType' => 'select',
            'foreignKey' => 'tl_category.title',
            'eval' => array('mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'),
            'relation' => array('type' => 'hasOne', 'load' => 'lazy'),
            'sql' => "int(10) unsigned NOT NULL default '0'"
        )
    )
);

?>