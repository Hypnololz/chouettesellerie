<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class CreateNewsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' =>'titre',
                'constraints' => [
                    new notBlank([
                        'message' => 'Merci de renseigner un titre'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' =>'Le titre doit contenir au moins {{ limit }} caractères',
                        'max' => 100,
                        'maxMessage' =>'le titre doit contenir au moins {{ limit }} caractères',
                        ]),
                ]
            ])

            ->add('content', CKEditorType::class, [
                    'label' =>'Contenu',
                    'purify_html' => true,
                    'constraints' => [
                        new notBlank([
                            'message' => 'Merci de renseigner un titre'
                        ]),
                        new Length([
                            'min' => 2,
                            'minMessage' =>'Le texte doit contenir au moins {{ limit }} caractères',
                            'max' => 100,
                            'maxMessage' =>'le texte doit contenir au moins {{ limit }} caractères',
                        ]),
                    ]
            ])


            ->add('photo',TextType::class,[
                'label' => 'photo'
                ])

            ->add('save',SubmitType::class,[
                'label'=>'créer article'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
