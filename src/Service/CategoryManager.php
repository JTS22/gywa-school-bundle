<?php
declare(strict_types=1);

namespace GyWa\SchoolBundle;


use Doctrine\DBAL\Connection;
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

    public function getAllCategories() : array {
        $result = array();

        $statement = $this->database->prepare("SELECT id FROM tl_category");
        $statement->execute();
        while ($idObj = $statement->fetch(FetchMode::STANDARD_OBJECT)) {
            array_push($result, $idObj->id);
        }

        return $result;
    }

}