<?php
namespace BlogEcrivain\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
class PostWrite extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('title', TextType::class)
				->add('content', TextareaType::class, array('required' => false,))
				->add('publish', CheckboxType::class, array('required' => false,));
	}
	
	public function getName() {
		return 'post';
	}
}