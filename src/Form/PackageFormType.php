<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\BusinessType;
use App\Entity\Category;
use App\Entity\Package;
use App\Entity\PackageImage;
use App\Repository\PackageImageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackageFormType extends AbstractType
{
    public function __construct(
        private PackageImageRepository $packageImageRepository,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $business = $options['business'];

        $builder->add('name', TextType::class)
                ->add('description', TextareaType::class)
                ->add('price', NumberType::class)
                ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name'
                ])
                ->add('image', EntityType::class, [
                    'class' => PackageImage::class,
                    'choice_label' => false,
                    'required' => false,
                    'expanded' => true,
                    'choice_attr' => function (PackageImage $packageImage) {
                        return ['data-url' => $packageImage->getPath()];
                    },
                    'query_builder' => function () use ($business) {
                        return $this->packageImageRepository
                            ->createQueryBuilder('i')
                            ->where('i.business = :business')
                            ->setParameter('business', $business);
                    },
                ])
                ->add('uploadedImage', FileType::class, [
                    'label' => 'Upload new image',
                    'mapped' => false,
                    'required' => false,
                    'attr' => [
                        'accept' => "image/*"
                    ]
                ])
                ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Package::class,
            'business' => null,
        ]);
    }
}
