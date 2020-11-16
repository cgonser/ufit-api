<?php

namespace App\Vendor\Repository;

use App\Vendor\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function save(Question $question)
    {
        $this->getEntityManager()->persist($question);
        $this->getEntityManager()->flush();
    }

    public function delete(Question $question)
    {
        $this->getEntityManager()->remove($question);
        $this->getEntityManager()->flush();
    }
}
