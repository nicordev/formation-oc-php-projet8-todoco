<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    /**
     * @var array
     */
    private $translatedRoles = [];

    public function __construct(array $securityRoleHierarchyRoles, array $translations)
    {
        $availableRoles = array_keys($securityRoleHierarchyRoles);
        $translatedRoles = array_keys($translations);

        if ([] !== $missingTranslations = array_diff($availableRoles, $translatedRoles)) {
            throw new \InvalidArgumentException("Missing translations: " . print_r($missingTranslations, true));
        }

        $this->translatedRoles = $translations;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => array_flip($this->translatedRoles)
            ])
        ;
    }
}
