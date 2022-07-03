<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\ManyToMany(targetEntity: Accessoire::class, inversedBy: 'orders')]
    private $accessoires;

    #[ORM\ManyToMany(targetEntity: Barbecue::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
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

    public function __construct()
    {
        $this->accessoires = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

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
        }

        return $this;
    }

    public function removeAccessoire(Accessoire $accessoire): self
    {
        $this->accessoires->removeElement($accessoire);

        return $this;
    }

    public function getBarbecue(): ?barbecue
    {
        return $this->barbecue;
    }

    public function setBarbecue(?barbecue $barbecue): self
    {
        $this->barbecue = $barbecue;

        return $this;
    }
}
