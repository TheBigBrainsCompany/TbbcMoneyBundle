Form and Doctrine Integration
=============================

Let's see an example :
* an entity with a setPrice / getPrice manipulating a Money object
* a controller with the form

### Doctrine entity

Note that there is 2 columns : $priceAmount and $priceCurrency and only one get/set : getPrice and setPrice.

The get/setPrice are dealing with these two columns transparently.

```php
<?php
namespace App\AdministratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * TestMoney
 *
 * @ORM\Table("test_money")
 * @ORM\Entity
 */
class TestMoney
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="price_amount", type="integer")
     */
    private $priceAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="price_currency", type="string", length=64)
     */
    private $priceCurrency;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TestMoney
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get Money
     *
     * @return Money
     */
    public function getPrice()
    {
        if (!$this->priceCurrency) {
            return null;
        }
        if (!$this->priceAmount) {
            return new Money(0, new Currency($this->priceCurrency));
        }
        return new Money($this->priceAmount, new Currency($this->priceCurrency));
    }

    /**
     * Set price
     *
     * @param Money $price
     * @return TestMoney
     */
    public function setPrice(Money $price)
    {
        $this->priceAmount = $price->getAmount();
        $this->priceCurrency = $price->getCurrency()->getName();

        return $this;
    }
}
```

### Controller
```php
public function testMoneyFormAction()
{
    // my doctrine entity
    $testMoney = new TestMoney();

    // I create my form
    $form = $this->createFormBuilder($testMoney)
        ->add("name", 'text')
        ->add("price", "tbbc_money")
        ->add("save", "submit")
        ->getForm();

    // post handling
    $form->handleRequest($this->getRequest());
    if ($form->isValid()) {
        // fait quelque chose comme sauvegarder la tâche dans la bdd
        $em = $this->getDoctrine()->getManager();
        $em->persist($testMoney);
        $em->flush();
        return $this->redirect($this->generateUrl('app_administrator_default_testmoneyform'));
    }

    // render form
    return $this->render("AppAdministratorBundle:Default:testMoneyForm.html.twig", array(
        "form" => $form->createView()
    ));
}
```
