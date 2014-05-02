<?

class Participant {
  
  var $first_name;
  var $family_name;
  var $email;

  public function __construct($first_name, $family_name, $email) {
    $this->first_name = $first_name;
    $this->family_name = $family_name;
    $this->email = $email;
  }

  public function matches($other) {
    if ( ($this->first_name == $other->first_name) && 
         ($this->family_name == $other->family_name)) {
      return true;
    } else {
      return false;
    }
  }

  public function inSameFamilyAs($other) {
    if (($this->family_name == $other->family_name)) {
      return true;
    } else {
      return false;
    }
  }

  public function displayName() {
    return $this->first_name . " " . $this->family_name;
  }

}

?>