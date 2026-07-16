<?php

namespace App\Form;

use App\Constant\Cities;
use App\Dto\PackageSearchFilter;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackageFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', SearchType::class, [
                'required' => false,
                'label' => 'Name'
            ])
            ->add('minPrice', NumberType::class, [
                'required' => false,
                'label' => 'Min price'
            ])
            ->add('maxPrice', NumberType::class, [
                'required' => false,
                'label' => 'Max price'
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('city', ChoiceType::class, [
                'choices' => array_combine(Cities::LIST, Cities::LIST),
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PackageSearchFilter::class,
        ]);
    }
}
