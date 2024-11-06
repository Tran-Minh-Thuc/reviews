<?php

namespace App\Form;

use App\Entity\Movies;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class MovieType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('title', TextType::class, [
        'label' => 'Tiêu đề',
        'attr' => [
          'class' => 'form-control',
        ],
        'constraints' => [
          new NotBlank(['message' => 'Tiêu đề không được để trống.']),
        ],
      ])
      ->add('description', TextType::class, [
        'label' => 'Mô tả',
        'attr' => [
          'class' => 'form-control',
        ],
      ])
      ->add('img')
      ->add('save', SubmitType::class, [
        'label' => 'Lưu phim',
        'attr' => [
          'class' => 'btn btn-primary me-2',
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Movies::class,
    ]);
  }
}
