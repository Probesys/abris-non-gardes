<?php

namespace App\Service;

use App\Entity\Abris;
use App\Entity\Dysfonctionnement;
use App\Entity\User;
use App\Entity\UploadedDocument;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader {

    private $slugger;
    private $em;
    private $targetDirectory;

    public function __construct($targetDirectory, EntityManager $em) {
        $this->slugger = new Slugify();
        $this->targetDirectory = $targetDirectory;
        $this->em = $em;
    }

    public function upload(UploadedFile $file, $obj) {
        $targetDirectory = null;
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $this->slugger->slugify($file->getClientOriginalName()) . '-' . uniqid() . '.' . $file->guessExtension();

        $photo = new UploadedDocument();
        switch (true) {
            case $obj instanceof Abris:
                $photo->setAbris($obj);
                $targetDirectory = $this->targetDirectory . "/" . $obj->getId();
                break;
            case $obj instanceof Dysfonctionnement:
                $targetDirectory = $this->targetDirectory . "/" . $obj->getAbris()->getId();
                $photo->setDysfonctionnement($obj);
                break;
            case $obj instanceof User:
                $targetDirectory = $this->targetDirectory . "/" . $obj->getId();
                if($obj->getPhoto()){
                    // update photo
                    $photo = $obj->getPhoto();
                } else{
                    $photo->setUser($obj);
                    $obj->setPhoto($photo);
                }
                break;
        }
        
        $photo->setFileName($fileName);
        $photo->setMimeType($file->getMimeType());
        $photo->setFilesize($file->getSize());
//                    $uploadedDoc->move($directory);
        try {
            $file->move($targetDirectory , $fileName);   
            $this->em->persist($photo);
        } catch (FileException $e) {
            //Todo
            //dump($e);
            //die;
        }
        


        return $fileName;
    }
    
    public function setTargetDirectory($dirname) {
        $this->targetDirectory = $this->targetDirectory.$dirname;
        return $this;
    }

}
