<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public const ROLE_TRANSLATIONS = [
        "Utilisateur" => "ROLE_USER",
        "Administrateur" => "ROLE_ADMIN"
    ];
    public const OPTION_PASSWORD_REQUIRED = "password_required";

    public function __construct(array $securityRoleHierarchyRoles)
    {
        $availableRoles = array_keys($securityRoleHierarchyRoles);

        if ([] !== $missingTranslations = array_diff($availableRoles, self::ROLE_TRANSLATIONS)) {
            throw new \InvalidArgumentException("Missing translations: " . print_r($missingTranslations, true));
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => $options[self::OPTION_PASSWORD_REQUIRED],
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => self::ROLE_TRANSLATIONS
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            self::OPTION_PASSWORD_REQUIRED => true
        ]);
        $resolver->setAllowedTypes(self::OPTION_PASSWORD_REQUIRED, 'bool');
    }
}
