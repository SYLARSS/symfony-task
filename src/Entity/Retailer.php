<?php

namespace App\Entity;

use App\Repository\RetailerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=RetailerRepository::class)
 * @ORM\Table(name="`retailer`")
 */
class Retailer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="dealer_name")
     */
    private $dealerName;

    /**
     * @ORM\Column(type="string", length=255, name="dealer_number")
     */
    private $dealerNumber;

    /**
     * @ORM\Column(type="integer", name="user_id")
     */
    private $userId;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="retailers", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="retailer")
     */

    private $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking)
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRetailer($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getRetailer() === $this) {
                $booking->setRetailer(null);
            }
        }
        return $this;
    }

    public function getDealerName()
    {
        return $this->dealerName;
    }

    public function setDealerName($name)
    {
        $this->dealerName = $name;
        return $this;
    }

    public function getDealerNumber()
    {
        return $this->dealerNumber;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setDealerNumber($number)
    {
        $this->dealerNumber = $number;
        return $this;
    }
}
