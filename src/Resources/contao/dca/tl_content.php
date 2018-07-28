<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['subject_list'] =
    '{type_legend},type,headline;{subject_legend}gywa_display_category_toggler,gywa_category_filter;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['gywa_display_category_toggler'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywa_display_category_toggler'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50 m12'),
    'default' => true,
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywa_category_filter'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywa_category_filter'],
    'foreignKey' => 'tl_category.title',
    'inputType' => 'checkboxWizard',
    'default' => System::getContainer()->get('gywaschoolbundle.categoryManager')->getAllCategories(),
    'eval' => array('multiple' => true),
    'sql' => "blob NULL",
    'relation' => array('type' => 'belongsToMany', 'load' => 'lazy')
);

?>