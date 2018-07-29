<?php


namespace GyWa\SchoolBundle\Controller\ContentElement;


use Contao\ContentModel;

use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;

use Contao\FilesModel;

use Contao\FrontendTemplate;

use Contao\Template;

use Doctrine\DBAL\Connection;

use Doctrine\DBAL\FetchMode;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\Uuid;


class TeacherListController extends AbstractContentElementController
{

    private $database;

    public function __construct(Connection $db)
    {
        $this->database = $db;
    }


    private function escapeHTML($string)
    {
        return str_replace(['ä', 'ö', 'ü', '_', ' '], ['ae', 'oe', 'ue', '-', '-'], mb_strtolower($string));
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $subjectStatement = $this->database->prepare('SELECT * FROM tl_subject');
        $subjectStatement->execute();
        $result = $subjectStatement->fetchAll(FetchMode::ASSOCIATIVE);
        $subjectStatement->closeCursor();

        $allSubjects = array();

        if (!empty($result)) {

            foreach ($result as $row) {

                $allSubjects[$this->escapeHTML($row['abbreviation'])] = [
                    'referencePage' => $row['referencePage'],
                    'abbreviation' => $row['abbreviation'],
                    'title' => $row['title']
                ];
            }
        }

        unset($subjectStatement);
        $subjectStatement = $this->database->prepare('SELECT abbreviation FROM tl_subject WHERE id = ? LIMIT 1');

        $teacherStatement = $this->database->prepare('SELECT * FROM tl_teacher WHERE category = ? ORDER BY lastName ASC');
        $allCategories = array();

        $categoryStatement = $this->database->prepare("SELECT * FROM tl_teacher_category");
        $categoryStatement->execute();
        while ($category = $categoryStatement->fetch(FetchMode::STANDARD_OBJECT)) {

            $allTeachers = array();
            $teacherStatement->execute([$category->id]);

            $result = $teacherStatement->fetchAll(FetchMode::ASSOCIATIVE);

            if (!empty($result)) {
                foreach ($result as $row) {
                    $name = $row['lastName'] . ',' . (strlen($row['lastName']) < 15 ? '<br>' : ' ') . (!is_null($row['prefix']) ? $row['prefix'] . '&nbsp;' : '') . $row['firstName'];
                    $subjects = array();

                    if ($row['subjects']) {
                        foreach (unserialize($row['subjects']) as $subjectID) {
                            $subjectStatement->execute([$subjectID]);
                            $result = $subjectStatement->fetch(FetchMode::ASSOCIATIVE);
                            $subjects[] = $this->escapeHTML($result['abbreviation']);
                        }
                        sort($subjects);
                    }
                    $emailAddress = $this->escapeHTML(substr($row['firstName'], 0, 1) . '.' . $row['lastName']);

                    if ($row['image']) {
                        $imagePath = FilesModel::findByUuid($row['image'])->path;
                    } else {
                        $imagePath = FilesModel::findByUuid($model->gywaDefaultTeacherImage)->path;
                    }

                    array_push($allTeachers, array(
                        'name' => $name,
                        'abbreviation' => $row['abbreviation'],
                        'subjects' => $subjects,
                        'image' => $imagePath,
                        'emailAddress' => $emailAddress
                    ));
                    unset($subjects);
                }
            }

            $categoryName = str_replace(["ä", "ö", "ü", "_", " ", "/", ","], ["ae", "oe", "ue", "-", "-", "-", "-"], mb_strtolower($category->title));
            array_push($allCategories, array(
                'title' => $category->title,
                'alias' => $categoryName,
                'cssClass' => $category->cssClass,
                'items' => $allTeachers));

        }

        $togglerTemplate = new FrontendTemplate('ce_category_toggler');
        $togglerTemplate->setData(array('categoryList' => $allCategories, 'allName' => sprintf($GLOBALS['TL_LANG']['MSC']['gywa_category']['all'])));

        $template->categories = $allCategories;
        $template->emailDomain = $model->gywaTeacherEmailDomain;
        $template->togglerCode = $togglerTemplate->parse();
        $template->subjects = $allSubjects;

        return $template->getResponse();

    }


}