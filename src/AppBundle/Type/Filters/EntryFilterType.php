<?php
/**
 * User: Joan Teixidó <joan@laiogurtera.com>
 * Date: 2/2/16
 * Time: 22:50
 */

namespace AppBundle\Type\Filters;

use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('concept', TextFilterType::class, array(
            'condition_pattern' => FilterOperands::STRING_CONTAINS
        ))

        ->add('date', DateRangeFilterType::class, array(
            'left_date_options' => array(
                'widget' => 'single_text',
            ),
            'right_date_options' => array(
                'widget' => 'single_text',
            )
        ))
        ;
    }

    public function getName()
    {
        return 'entry_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
{

}