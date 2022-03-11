<?php

namespace App\DTO;

class RegistrationSearchFormDTO
{
   private $dni;
   private $course;
   private $active;

   /**
    * Get the value of dni
    */ 
    public function getDni()
    {
       return $this->dni;
    }
 
    /**
     * Set the value of dni
     *
     * @return  self
     */ 
    public function setDni($dni)
    {
       $this->dni = $dni;
 
       return $this;
    }
 
   /**
    * Get the value of course
    */ 
   public function getCourse()
   {
      return $this->course;
   }

   /**
    * Set the value of course
    *
    * @return  self
    */ 
   public function setCourse($course)
   {
      $this->course = $course;

      return $this;
   }

   /**
    * Get the value of active
    */ 
   public function getActive()
   {
      return $this->active;
   }

   /**
    * Set the value of active
    *
    * @return  self
    */ 
   public function setActive($active)
   {
      $this->active = $active;

      return $this;
   }
}