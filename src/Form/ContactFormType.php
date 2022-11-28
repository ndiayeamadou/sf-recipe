<?php

namespace App\Form;

use App\Entity\Contact;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => ['class' => 'mt-3'],
                'label' => 'Nom Complet',
                'constraints' => [new Assert\Length(['min'=>'5'])],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Adresse email',
                'label_attr' => ['class' => 'mt-3'],
                'constraints' => [new Assert\Length(['min'=>'8'])]
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Objet du message',
                'constraints' => [new Assert\Length(['min'=>'5'])],
                'label_attr' => ['class' => 'mt-3'],
                //'required' => false
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Votre message',
                'label_attr' => ['class' => 'mt-3'],
                'constraints' => [new Assert\Length(['min'=>'5'])]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'form-control btn btn-lg btn-primary mt-4'
                ],
                'label' => 'Envoyer'
            ])
            /* ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
                'locale' => 'fr'
            ]) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
