<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\PropertySearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maxPrice', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Budget max'
                ]
            ])
            ->add('minSurface', IntegerType::class, [
                'required' => false, // ce champ n'est pas obligatoire
                'label' => false,    // pas de label
                'attr' => [
                    'placeholder' => 'Surface minimale'
                ]
            ])
            ->add('options',EntityType::class,[
                'required' => false,
                'multiple' => true,
                'choice_label' => 'name',
                'label' => false,
                'class' => Option::class // classe cible
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'method' => 'get', // Pour pouvoir partager la requête il faut une méthode GET
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix() // raccourcir l'url
    {
        return '';
    }
}
