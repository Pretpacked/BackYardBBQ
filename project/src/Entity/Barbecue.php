<?php

namespace App\Entity;

use App\Repository\BarbecueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: BarbecueRepository::class)]
class Barbecue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['huren'])]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['huren'])]
    private $image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['huren'])]
    private $name;

    #[ORM\Column(type: 'text')]
    #[Groups(['huren'])]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['huren'])]
    private $type;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'barbecues')]
    #[Groups(['huren'])]
    private $orders;

    #[ORM\Column(type: 'integer')]
    #[Groups(['huren'])]
    private $barbecue_price;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addBarbecue($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeBarbecue($this);
        }

        return $this;
    }

    public function getBarbecuePrice(): ?int
    {
        return $this->barbecue_price;
    }

    public function setBarbecuePrice(int $barbecue_price): self
    {
        $this->barbecue_price = $barbecue_price;

        return $this;
    }
}
