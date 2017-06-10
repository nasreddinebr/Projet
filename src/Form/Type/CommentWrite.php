<?php
namespace BlogEcrivain\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentWrite extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('content', TextareaType::class);
	}
	
	public function getUser() {
		return 'comment';
	}
}