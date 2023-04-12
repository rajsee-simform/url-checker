<?php

namespace App\Form;

use App\Entity\Urls;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class URLType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file_uploader', FileType::class, [
                'label' => 'Upload CSV file',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'accept' => 'text/csv', // Restrict to only accept CSV files
                ],
            ])
            ->add('submit', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
