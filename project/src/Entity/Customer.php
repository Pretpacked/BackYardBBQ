<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $adress;

    #[ORM\Column(type: 'integer')]
    private $phone_number;

    #[ORM\ManyToMany(targetEntity: Barbecue::class, inversedBy: 'customers')]
    private $barbecue;

    #[ORM\Column(type: 'date')]
    private $orderd_date;

    #[ORM\Column(type: 'date')]
    private $start_date;

    #[ORM\Column(type: 'date')]
    private $end_date;

    #[ORM\Column(type: 'integer')]
    private $price_total;

    #[ORM\Column(type: 'text')]
    private $remark;

    #[ORM\ManyToMany(targetEntity: Accessoire::class, mappedBy: 'customer')]
    private $accessoires;

    public function __construct()
    {
        $this->barbecue = new ArrayCollection();
        $this->accessoires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(int $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    /**
     * @return Collection<int, barbecue>
     */
    public function getBarbecue(): Collection
    {
        return $this->barbecue;
    }

    public function addBarbecue(barbecue $barbecue): self
    {
        if (!$this->barbecue->contains($barbecue)) {
            $this->barbecue[] = $barbecue;
        }

        return $this;
    }

    public function removeBarbecue(barbecue $barbecue): self
    {
        $this->barbecue->removeElement($barbecue);

        return $this;
    }

    public function getOrderdDate(): ?\DateTimeInterface
    {
        return $this->orderd_date;
    }

    public function setOrderdDate(\DateTimeInterface $orderd_date): self
    {
        $this->orderd_date = $orderd_date;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getPrice_total(): ?int
    {
        return $this->price_total;
    }

    public function setPrice_total(int $price_total): self
    {
        $this->price_total = $price_total;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * @return Collection<int, Accessoire>
     */
    public function getAccessoires(): Collection
    {
        return $this->accessoires;
    }

    public function addAccessoire(Accessoire $accessoire): self
    {
        if (!$this->accessoires->contains($accessoire)) {
            $this->accessoires[] = $accessoire;
            $accessoire->addCustomer($this);
        }

        return $this;
    }

    public function removeAccessoire(Accessoire $accessoire): self
    {
        if ($this->accessoires->removeElement($accessoire)) {
            $accessoire->removeCustomer($this);
        }

        return $this;
    }
}
