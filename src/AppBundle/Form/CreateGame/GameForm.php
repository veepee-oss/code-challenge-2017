<?php

namespace AppBundle\Form\CreateGame;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GameForm
 *
 * @package AppBundle\Form\CreateGame
 */
class GameForm extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\AppBundle\Form\CreateGame\GameEntity'
        ));
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('height', '\Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
            'label' => 'app.createpage.form.height'
        ));

        $builder->add('width', '\Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
            'label' => 'app.createpage.form.width'
        ));

        $builder->add('players', '\Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
            'label' => 'app.createpage.form.players'
        ));

        $builder->add('minGhosts', '\Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
            'label' => 'app.createpage.form.min-ghosts'
        ));

        $builder->add('ghostRate', '\Symfony\Component\Form\Extension\Core\Type\IntegerType', array(
            'label' => 'app.createpage.form.ghost-rate'
        ));

        $builder->add('save', '\Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
            'label' => 'app.createpage.form.create'
        ));
    }
}
