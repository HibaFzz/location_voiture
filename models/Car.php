<?php

class Car {
    private $id;
    private $matricule;
    private $image;
    private $vehicletitle;
    private $brand;
    private $vehicleoverview;
    private $priceperday;
    private $fueltype;
    private $modelyear;
    private $nbrpersonne;
    private $disponible; 

    public function __construct($id, $matricule, $image, $vehicletitle, $brand, $vehicleoverview, $priceperday, $fueltype, $modelyear, $nbrpersonne, $disponible) {
        $this->id = $id;
        $this->matricule = $matricule;
        $this->image = $image;
        $this->vehicletitle = $vehicletitle;
        $this->brand = $brand;
        $this->vehicleoverview = $vehicleoverview;
        $this->priceperday = $priceperday;
        $this->fueltype = $fueltype;
        $this->modelyear = $modelyear;
        $this->nbrpersonne = $nbrpersonne;
        $this->disponible = $disponible; // Initialize the new property
    }

    // Getter and Setter for disponible
    public function getDisponible() {
        return $this->disponible;
    }

    public function setDisponible($disponible) {
        $this->disponible = $disponible;
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function getMatricule() {
        return $this->matricule;
    }

    public function setMatricule($matricule) {
        $this->matricule = $matricule;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function getVehicletitle() {
        return $this->vehicletitle;
    }

    public function setVehicletitle($vehicletitle) {
        $this->vehicletitle = $vehicletitle;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function setBrand($brand) {
        $this->brand = $brand;
    }

    public function getVehicleoverview() {
        return $this->vehicleoverview;
    }

    public function setVehicleoverview($vehicleoverview) {
        $this->vehicleoverview = $vehicleoverview;
    }

    public function getPriceperday() {
        return $this->priceperday;
    }

    public function setPriceperday($priceperday) {
        $this->priceperday = $priceperday;
    }

    public function getFueltype() {
        return $this->fueltype;
    }

    public function setFueltype($fueltype) {
        $this->fueltype = $fueltype;
    }

    public function getModelyear() {
        return $this->modelyear;
    }

    public function setModelyear($modelyear) {
        $this->modelyear = $modelyear;
    }

    public function getNbrpersonne() {
        return $this->nbrpersonne;
    }

    public function setNbrpersonne($nbrpersonne) {
        $this->nbrpersonne = $nbrpersonne;
    }
}
