<?php
namespace BlogEcrivain\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserSignUp extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('username', TextType::class)
				->add('email', EmailType::class)
				->add('password', RepeatedType::class, array(
					'type'		=> PasswordType::class,
					'invalid_message'	=> 'Les champs de mot de passe doivent correspondre',
					'options'			=> array('required' => true),
					'first_options'		=> array('label' => 'Password'),
					'second_options'	=> array('label' => 'répéter votre password'),
		));
	}
	
	public function getName() {
		return 'user';
	}
}