<?php

namespace GyWa\SchoolBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\FrontendTemplate;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubjectListController extends AbstractContentElementController
{

    private $database;

    public function __construct(Connection $db)
    {
        $this->database = $db;
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $categoryStatement = $this->database->prepare("SELECT * FROM tl_category");
        $categoryStatement->execute();

        $subjectStatement = $this->database->prepare("SELECT * FROM tl_subject WHERE category=? ORDER BY title ASC");

        $arrProperties = array();
        $allCategories = array();

        while ($category = $categoryStatement->fetch()) {
            $subjectStatement->execute(array($category['id']));

            $allSubjects = array();

            while ($subject = $subjectStatement->fetch()) {
                array_push($allSubjects, array(
                    'title' => $subject['title'],
                    'abbreviation' => strtolower($subject['abbreviation']),
                    'page' => $subject['referencePage'],
                    'css' => $subject['cssClass']
                ));
            }

            $categoryName = str_replace(["ä", "ö", "ü", "_", " ", "/", ","], ["ae", "oe", "ue", "-", "-", "-", "-"], mb_strtolower($category['title']));

            $arrProperties[$categoryName] = array(
                // TODO extract into method/service
                'displayName' => $category['title'],
                'cssClass' => $category['cssClass'],
                'items' => $allSubjects
            );

            array_push($allCategories, array('title' => $category['title'], 'alias' => $categoryName));
        }

        $template->arrProperties = $arrProperties;

        $togglerTemplate = new FrontendTemplate('ce_category_toggler');
        $togglerTemplate->setData(array('categoryList' => $allCategories, 'allName' => sprintf($GLOBALS['TL_LANG']['MSC']['gywa_category']['all'])));

        $template->togglerCode = $togglerTemplate->parse();

        return $template->getResponse();
    }

}