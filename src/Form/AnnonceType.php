<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends ApplicationType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder'=>"titre de votre annonce"
                ]
            ])
            ->add('slug', TextType::class, $this->getConfiguration('slug','Adresse web (automatique)',[
                'required' => false
            ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Url de l\'image','Donnez l\'adresse de votre image'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction','Donnez une description globale de l\'annonce'))
            ->add('content', TextareaType::class, $this->getConfiguration('Description détaillée','Donnez une description de votre bien'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambres','Donnez le nombre de chambres disponibles'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix par nuit','indiquez le prix que vous voulez pour une nuit'))
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true, // permet d'ajouter de nouveaux éléments et ajouter un data_prototype (HTML)
                    'allow_delete' => true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}