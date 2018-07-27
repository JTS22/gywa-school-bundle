<?php

/**
 * Table tl_subject
 */
$GLOBALS['TL_DCA']['tl_teacher'] = array
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
            'fields' => array('lastName', 'firstName'),
            'flag' => 1,
            'panelLayout' => 'filter;sort,search,limit'
        ),
        'label' => array
        (
            'fields' => array('lastName', 'prefix', 'firstName', 'abbreviation'),
            'format' => '%s, %s %s (%s)',
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
                'label' => &$GLOBALS['TL_LANG']['tl_teacher']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'copy' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_teacher']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.svg'
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_teacher']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_teacher']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
                'attributes' => 'style="margin-right:3px"'
            ),
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},firstName,lastName,prefix,abbreviation,cssClass,image,{subjects_legend},subjects'
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
        'lastName' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['lastName'],
            'inputType' => 'text',
            'sorting' => true,
            'flag' => 1,
            'search' => true,
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 50,
                'tl_class' => 'w50'
            ),
            'sql' => "varchar(50) NOT NULL default ''"
        ),
        'firstName' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['firstName'],
            'inputType' => 'text',
            'sorting' => true,
            'flag' => 1,
            'search' => true,
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 50,
                'tl_class' => 'w50'
            ),
            'sql' => "varchar(50) NOT NULL default ''"
        ),
        'prefix' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['prefix'],
            'inputType' => 'text',
            'eval' => array(
                'maxlength' => 20,
                'tl_class' => 'w50',
            ),
            'sql' => "varchar(20) default ''"
        ),
        'abbreviation' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['abbreviation'],
            'inputType' => 'text',
            'exclude' => true,
            'search' => true,
            'eval' => array(
                'mandatory' => true,
                'unique' => true,
                'maxlength' => 4,
                'tl_class' => 'w50'
            ),
            'sql' => "varchar(4) BINARY NULL"
        ),
        'cssClass' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['cssClass'],
            'inputType' => 'text',
            'eval' => array('maxlenght' => 64, 'tl_class' => 'w50'),
            'sql' => "varchar(64) NOT NULL default ''"
        ),
        'image' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['image'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => array('filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr', 'extensions' => Config::get('validImageTypes')),
            'sql' => "binary(16) NULL"
        ),
        'subjects' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_teacher']['subjects'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkboxWizard',
            'foreignKey' => 'tl_subject.title',
            'eval' => array('multiple' => true),
            'sql' => "blob NULL",
            'relation' => array('type' => 'belongsToMany', 'load' => 'lazy')
        ),
    )
);


?>