<?php

namespace App\Service;

use App\Entity\Business;
use App\Entity\PackageImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    public function uploadPackageImage(UploadedFile $file, Business $business, EntityManagerInterface $em): PackageImage
    {
        $filename = uniqid() . '-' . $file->getClientOriginalName();
        $directory = 'uploads/business/' . $business->getId() . '/package';
        $publicPath = '/' . $directory . '/' . $filename;
        $file->move($directory, $filename);

        $packageImage = new PackageImage();
        $packageImage->setBusiness($business);
        $packageImage->setPath($publicPath);

        $em->persist($packageImage);

        return $packageImage;
    }
}
