<?php
namespace BlogEcrivain\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAdminWrite extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('username', TextType::class)
				->add('email', EmailType::class)
				->add('password', RepeatedType::class, array(
						'type'		=> PasswordType::class,
						'invalid_message'	=> 'Les champs de mot de passe doivent correspondre',
						'options'			=> array('required' => true),
						'first_options'		=> array('label' => 'Password'),
						'second_options'	=> array('label' => 'répéter votre password'),
				))
				->add('role', ChoiceType::class, array(
						'choices' => array(
							'User' 		=> 'ROLE_USER',
							'Moderator' => 'ROLE_MODERATOR',
							'Author' 	=> 'ROLE_AUTHOR',
							'Admin' 	=> 'ROLE_ADMIN')
				));
	}
	
	public function getName() {
		return 'user';
	}
}