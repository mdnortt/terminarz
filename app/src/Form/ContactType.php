<?php
/**
 * Contact type.
 */

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactType.
 */
class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );

        $builder->add(
            'phone',
            TextType::class,
            [
                'label' => 'label.phone',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );

        $builder->add(
            'email',
            TextType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => ['max_length' => 180],
            ]
        );

        $builder->add(
            'adress',
            TextType::class,
            [
                'label' => 'label.adress',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Contact::class]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'contact';
    }
}
