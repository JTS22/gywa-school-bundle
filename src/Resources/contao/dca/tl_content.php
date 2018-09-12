<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['subject_list'] =
    '{type_legend},type,headline;{subject_legend},gywaDisplayCategoryToggler;{filter_legend:hide},gywaCategoryFilter,gywaDisplayEmptyCategories;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['teacher_list'] =
    '{type_legend},type,headline;{teacher_legend},gywaDisplayCategoryToggler,gywaTeacherEmailDomain,gywaDefaultTeacherImage;{filter_legend:hide},gywaTeacherCategoryFilter,gywaSubjectFilter,gywaSubjectFilterRequireAll,gywaDisplayEmptyCategories;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';


$GLOBALS['TL_DCA']['tl_content']['fields']['gywaDisplayCategoryToggler'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaDisplayCategoryToggler'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50 m12'),
    'default' => true,
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaCategoryFilter'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaCategoryFilter'],
    'foreignKey' => 'tl_category.title',
    'inputType' => 'checkboxWizard',
    'default' => System::getContainer()->get('gywaschoolbundle.categoryManager')->getAllSubjectCategories(),
    'eval' => array('multiple' => true),
    'sql' => "blob NULL",
    'relation' => array('type' => 'belongsToMany', 'load' => 'lazy')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaDefaultTeacherImage'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaDefaultTeacherImage'],
    'inputType' => 'fileTree',
    'eval' => array('mandatory' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr', 'extensions' => Config::get('validImageTypes')),
    'sql' => "binary(16) NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaTeacherCategoryFilter'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaCategoryFilter'],
    'foreignKey' => 'tl_teacher_category.title',
    'inputType' => 'checkboxWizard',
    'default' => System::getContainer()->get('gywaschoolbundle.categoryManager')->getAllTeacherCategories(),
    'eval' => array('multiple' => true),
    'sql' => "blob NULL",
    'relation' => array('type' => 'belongsToMany', 'load' => 'lazy', 'tl_class' => "w50")
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaTeacherEmailDomain'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaTeacherEmailDomain'],
    'inputType' => 'text',
    'eval' => array('mandatory' => true, 'tl_class' => 'w50'),
    'sql' => "VARCHAR(256) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaSubjectFilter'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaSubjectFilter'],
    'foreignKey' => 'tl_subject.title',
    'inputType' => 'checkboxWizard',
    'default' => System::getContainer()->get('gywaschoolbundle.subjectManager')->getAllSubjects(),
    'eval' => array('multiple' => true),
    'sql' => "blob NULL",
    'relation' => array('type' => 'belongsToMany', 'load' => 'lazy', 'tl_class' => "w50")
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaSubjectFilterRequireAll'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaSubjectFilterRequireAll'],
    'inputType' => 'checkbox',
    'default' => false,
    'eval' => array('tl_class' => 'w50'),
    'sql' => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gywaDisplayEmptyCategories'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_content']['gywaDisplayEmptyCategories'],
    'inputType' => 'checkbox',
    'default' => true,
    'eval' => array('tl_class' => 'w50'),
    'sql' => "char(1) NOT NULL default ''",
);


?>