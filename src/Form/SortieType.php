<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SortieType extends AbstractType
{

    private $security;
    private $villeRepository;

    /**
     * @param $security
     */
    public function __construct(Security $security,VilleRepository $villeRepository)
    {
        $this->security = $security;
        $this->villeRepository = $villeRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie : ',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Ville',
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'class' => "form-select"
                ]
            ])

            ->add('lieu', ChoiceType::class, [
                'placeholder' => 'Lieu (Choisir une ville)',
                'required' => false,
                'attr' => [
                    'class' => "form-select"
                ]
            ])
            ->add("addLieu", LieuType::class, [
                'mapped' => false,
                'label' => ' ',
                'required' => false,
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie : ',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'html5' => true,
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée de la sortie : ',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('nbInscriptionMax', NumberType::class, [
                'label' => 'Nombre de places : ',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Descriptions et infos : ',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('inscriptionAuto', CheckboxType::class, [
                'label' => 'Voulez-vous vous inscrire à la sortie ? ',
                'data' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-creer-sortie btn-outline-secondary'],
                'label' => 'Enregistrer'
            ])
            ->add('publier', SubmitType::class, [
                'attr' => ['class' => 'btn btn-creer-sortie btn-outline-secondary'],
                'label' => 'Publier'
            ])

        ;

        $formModifier = function (FormInterface $form, Ville $villes = null) {
            $lieux = null === $villes ? [] : $villes->getLieux();
            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choices' => $lieux->toArray(),
                'required' => false,
                'choice_label' => 'nom',
                'placeholder' => 'Lieu (Choisir une ville)',
                'label' => 'Lieu'
            ]);
        };

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $ville = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $ville);
            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
