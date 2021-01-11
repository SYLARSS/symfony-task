<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
     */
    private $user;

    /**
     * @ORM\Column(name="retailer_id", type="integer")
     */
    private $retailerId;

    /**
     * @ORM\ManyToOne(targetEntity="Retailer", inversedBy="bookings")
     */
    private $retailer;

    /**
     * @ORM\Column(name="ordered_start_time", type="datetime", nullable=true)
     */
    private $orderedStartTime;

    /**
     * @ORM\Column(name="ordered_end_time",type="datetime", nullable=true)
     */
    private $orderedEndTime;

    /**
     * @ORM\Column(type="datetime", name="recorded_start_time", nullable=true)
     */
    private $recordedStartTime;

    /**
     * @ORM\Column(type="datetime", name="recorded_end_time", nullable=true)
     */
    private $recordedEndTime;

    /**
     * @ORM\Column(type="datetime", name="canceled", nullable=true)
     */
    private $canceled;

    /**
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getRetailer()
    {
        return $this->retailer;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setRetailer(Retailer $retailer)
    {
        $this->retailer = $retailer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderedStartTime()
    {
        return $this->orderedStartTime;
    }

    public function setOrderedStartTime($orderedStartTime)
    {
        $this->orderedStartTime = $orderedStartTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderedEndTime()
    {
        return $this->orderedEndTime;
    }

    public function setOrderedEndTime($orderedEndTime)
    {
        $this->orderedEndTime = $orderedEndTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecordedStartTime()
    {
        return $this->recordedStartTime;
    }

    public function setRecordedStartTime($recordedStartTime)
    {
        $this->recordedStartTime = $recordedStartTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecordedEndTime()
    {
        return $this->recordedEndTime;
    }

    public function setRecordedEndTime($recordedEndTime)
    {
        $this->recordedEndTime = $recordedEndTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanceled()
    {
        return $this->canceled;
    }

    public function setCanceled($canceled)
    {
        $this->canceled = $canceled;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }


}
