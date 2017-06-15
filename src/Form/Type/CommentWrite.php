<?php
namespace BlogEcrivain\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CommentWrite extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('parent_id', HiddenType::class, array(
						'data' => 'abcdef',
				))
				->add('depth', HiddenType::class, array(
						'data' => 'abcde',
				))
				->add('content', TextareaType::class);
	}
	
	public function getUser() {
		return 'comment';
	}
}