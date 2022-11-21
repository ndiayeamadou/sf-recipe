<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeType extends AbstractType
{
    /** make construction to get current user in symfony form */
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'min-length' => '2', 'max-length' => '50',
                    'class' => 'form-control'
                ],
                'label' => 'Name',
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [
                    new Assert\NotBlank(), new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('time', IntegerType::class, [
                'attr' => ['class' => 'form-control', 'min' => 1, 'max' => 1440],
                'label_attr' => ['class' => 'mt-4'], 'required' => false,
                'constraints' => [new Assert\Positive(), new Assert\LessThan(1441), new Assert\GreaterThan(1)],
                'label' => 'Time (in minutes)'
            ])
            ->add('nbpers', IntegerType::class, [
                'attr' => ['min' => 1, 'max' => 50, 'class' => 'form-control'],
                'label' => 'Number of people', 'required' => false,
                'constraints' => [new Assert\Positive()], 'label_attr' => ['class' => 'mt-4']
            ])
            ->add('hard', RangeType::class, [
                'attr' => ['min' => 1, 'max' => 5, 'class' => 'form-control form-range'],
                'label' => 'Difficulty', 'required' => 'false',
                'constraints' => [new Assert\Positive()], 'label_attr' => ['class' => 'mt-4']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['min' => 2, 'max' => 250, 'class' => 'form-control'],
                'constraints' => [new Assert\Positive(), new Assert\NotBlank()], 'label_attr' => ['class' => 'mt-4']
            ])
            ->add('price', MoneyType::class, [
                'attr' => ['max' => '1000', 'class' => 'form-control'], 'required' => false,
                'constraints' => [new Assert\Positive()], 'label_attr' => ['class' => 'mt-4']
            ])
            ->add('isFavorite', CheckboxType::class, [
                'attr' => ['class' => 'ml-3'], 'required' => false,
                'label' => 'Favorite ?', 'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [new Assert\NotNull()],
                'required' => false
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Recipe image', 'label_attr' => ['class' => 'form-label mt-4'],
                'required' => false
            ])
            /* ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                // we can also use the query_builer
                'query_builder' => function (IngredientRepository $ir) {
                    return $ir->createQueryBuilder('u')->orderBy('u.name', 'ASC');
                },

                'choice_label' => 'name',

                'multiple' => true,
                'expanded' => 'true',
            ]) */
            /** we can also use the query_builer to customize query in order to display only ingredients created by user */
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                
                'query_builder' => function (IngredientRepository $ir) {
                    return $ir->createQueryBuilder('i')->where('i.user = :user' )
                                ->orderBy('i.name', 'ASC')
                                ->setParameter('user', $this->security->getUser());
                },
               
                'choice_label' => 'name',

                'multiple' => true,
                'expanded' => 'true',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'form-control col-md-3 mt-4 btn btn-primary mb-4'],
                'label' => 'Save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
