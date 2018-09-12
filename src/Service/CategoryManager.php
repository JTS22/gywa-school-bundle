<?php
declare(strict_types=1);

namespace GyWa\SchoolBundle;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Psr\Log\LoggerInterface;

class CategoryManager
{
    private $logger;
    private $database;

    /**
     * SubjectManager constructor.
     * @param $logger
     * @param $database
     */
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->database = $connection;
    }

    public function getAllTeacherCategories() : array {
        $result = array();
        try {
            $statement = $this->database->prepare("SELECT id FROM tl_teacher_category");
            if ($statement->execute()) {
                while ($idObj = $statement->fetch(FetchMode::STANDARD_OBJECT)) {
                    array_push($result, $idObj->id);
                }
            }
        } catch (DBALException $e) {
            $this->logger->error("Teacher categories could not be loaded from database! Please ensure the table tl_teacher_category exists!");
        }
        return $result;
    }

    public function getAllSubjectCategories() : array {
        $result = array();

        try {
            $statement = $this->database->prepare("SELECT id FROM tl_subject_category");
            if($statement->execute()) {
                while ($idObj = $statement->fetch(FetchMode::STANDARD_OBJECT)) {
                    array_push($result, $idObj->id);
                }
            }
        } catch (DBALException $e) {
            $this->logger->error("Teacher categories could not be loaded from database! Please ensure the table tl_subject_category exists!");
        }
        return $result;
    }

}