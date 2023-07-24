<?php

namespace App\Form;

use App\Entity\RestaurantPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RestaurantPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('pictureFile', FileType::class, [
            //     'label' => 'Picture',
            //     'required' => false,
            // ])
            // ->add('_delete', CheckboxType::class, [
            //     'label' => 'Delete this picture',
            //     'mapped' => false,
            //     'required' => false,
            // ]);
            ->add('pictureFile', VichFileType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RestaurantPicture::class,
            "allow_extra_fields" => true
        ]);
    }
}
