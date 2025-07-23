<?php
require '../includes/config.php';
require '../includes/auth.php';

// $page_title = "Manage Phones & Gadgets";

require '../includes/header.php';

// Handle POST update or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_phone'])) {
        $id = intval($_POST['phone_id']);
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $imei = $_POST['imei'];
        $storage = $_POST['storage'];
        $color = $_POST['color'];
        $condition = $_POST['condition'];
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE phones SET brand=?, model=?, imei=?, storage=?, color=?, `condition`=?, price=?, quantity=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssdisi", $brand, $model, $imei, $storage, $color, $condition, $price, $quantity, $status, $id);
        $stmt->execute();
        $stmt->close();

        echo "<div class='alert alert-success'>Phone updated successfully.</div>";
    }

    if (isset($_POST['delete_phone'])) {
        $id = intval($_POST['phone_id']);
        $stmt = $conn->prepare("DELETE FROM phones WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        echo "<div class='alert alert-danger'>Phone deleted successfully.</div>";
    }

    if (isset($_POST['update_gadget'])) {
        // Sanitize and update gadget
        $id = intval($_POST['gadget_id']);
        $name = $_POST['name'];
        $model = $_POST['model'];
        $serial_number = $_POST['serial_number'];
        $specifications = $_POST['specifications'];
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);

        $stmt = $conn->prepare("UPDATE gadgets SET name=?, model=?, serial_number=?, specifications=?, price=?, quantity=? WHERE id=?");
        $stmt->bind_param("ssssdii", $name, $model, $serial_number, $specifications, $price, $quantity, $id);
        $stmt->execute();
        $stmt->close();

        echo "<div class='alert alert-success'>Gadget updated successfully.</div>";
    }

    if (isset($_POST['delete_gadget'])) {
        $id = intval($_POST['gadget_id']);
        $stmt = $conn->prepare("DELETE FROM gadgets WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        echo "<div class='alert alert-danger'>Gadget deleted successfully.</div>";
    }
}

// Handle search query input
$search_type = $_GET['type'] ?? 'phones'; 
$search_term = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

?>

<div class="container my-4">

  <h2><?= htmlspecialchars($page_title) ?></h2>

  <!-- Nav tabs for Phones / Gadgets -->
  <ul class="nav nav-tabs mb-3" id="itemTab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link <?= ($search_type == 'phones') ? 'active' : '' ?>" href="?type=phones" role="tab">Phones</a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link <?= ($search_type == 'gadgets') ? 'active' : '' ?>" href="?type=gadgets" role="tab">Gadgets</a>
    </li>
  </ul>

  <!-- Search form -->
  <form method="get" class="row g-3 mb-4">
    <input type="hidden" name="type" value="<?= htmlspecialchars($search_type) ?>">
    
    <div class="col-md-3">
      <input type="text" name="search" class="form-control" placeholder="<?= ($search_type == 'phones') ? 'Brand or Model' : 'Name' ?>" value="<?= htmlspecialchars($search_term) ?>">
    </div>

    <div class="col-md-3">
      <input type="date" name="date_from" class="form-control" placeholder="From Date" value="<?= htmlspecialchars($date_from) ?>">
    </div>

    <div class="col-md-3">
      <input type="date" name="date_to" class="form-control" placeholder="To Date" value="<?= htmlspecialchars($date_to) ?>">
    </div>

    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
  </form>

  <?php
  // Build query and fetch results based on type

  if ($search_type === 'phones') {
      $query = "SELECT * FROM phones WHERE 1=1";
      $params = [];
      $types = '';

      if (!empty($search_term)) {
          $query .= " AND (brand LIKE ? OR model LIKE ?)";
          $params[] = "%$search_term%";
          $params[] = "%$search_term%";
          $types .= "ss";
      }

      if (!empty($date_from)) {
          $query .= " AND DATE(added_at) >= ?";
          $params[] = $date_from;
          $types .= "s";
      }

      if (!empty($date_to)) {
          $query .= " AND DATE(added_at) <= ?";
          $params[] = $date_to;
          $types .= "s";
      }

      $query .= " ORDER BY added_at DESC";

      $stmt = $conn->prepare($query);
      if ($params) {
          $stmt->bind_param($types, ...$params);
      }
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 0) {
          echo "<div class='alert alert-info'>No phones found.</div>";
      } else {
  ?>

  <table class="table table-bordered table-hover table-striped">
    <!-- <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Brand</th>
        <th>Model</th>
        <th>IMEI</th>
        <th>Storage</th>
        <th>Color</th>
        <th>Condition</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Added At</th>
        <th>Actions</th>
      </tr>
    </thead> -->
    <tbody>
      <?php while ($phone = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $phone['id'] ?></td>
          <td><?= htmlspecialchars($phone['brand']) ?></td>
          <td><?= htmlspecialchars($phone['model']) ?></td>
          <td><?= htmlspecialchars($phone['imei']) ?></td>
          <td><?= htmlspecialchars($phone['storage']) ?></td>
          <td><?= htmlspecialchars($phone['color']) ?></td>
          <td><?= htmlspecialchars($phone['condition']) ?></td>
          <td><?= number_format($phone['price'], 2) ?></td>
          <td><?= $phone['quantity'] ?></td>
          <td><?= htmlspecialchars($phone['status']) ?></td>
          <td><?= $phone['added_at'] ?></td>
          <td>
            <button 
              class="btn btn-sm btn-primary edit-phone-btn" 
              data-bs-toggle="modal" 
              data-bs-target="#editPhoneModal"
              data-phone='<?= json_encode($phone) ?>'
            >
              <i class="bi bi-pencil-square"></i>
            </button>
            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure to delete this phone?');">
              <input type="hidden" name="phone_id" value="<?= $phone['id'] ?>">
              <button type="submit" name="delete_phone" class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <?php 
      }
      $stmt->close();
  } else if ($search_type === 'gadgets') {
      $query = "SELECT * FROM gadgets WHERE 1=1";
      $params = [];
      $types = '';

      if (!empty($search_term)) {
          $query .= " AND (name LIKE ? OR model LIKE ?)";
          $params[] = "%$search_term%";
          $params[] = "%$search_term%";
          $types .= "ss";
      }

      if (!empty($date_from)) {
          $query .= " AND DATE(added_at) >= ?";
          $params[] = $date_from;
          $types .= "s";
      }

      if (!empty($date_to)) {
          $query .= " AND DATE(added_at) <= ?";
          $params[] = $date_to;
          $types .= "s";
      }

      $query .= " ORDER BY added_at DESC";

      $stmt = $conn->prepare($query);
      if ($params) {
          $stmt->bind_param($types, ...$params);
      }
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 0) {
          echo "<div class='alert alert-info'>No gadgets found.</div>";
      } else {
  ?>

  <table class="table table-bordered table-hover table-striped">
    <!-- <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Model</th>
        <th>Serial Number</th>
        <th>Specifications</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Added At</th>
        <th>Actions</th>
      </tr>
    </thead> -->
    <tbody>
      <?php while ($gadget = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $gadget['id'] ?></td>
          <td><?= htmlspecialchars($gadget['name']) ?></td>
          <td><?= htmlspecialchars($gadget['model']) ?></td>
          <td><?= htmlspecialchars($gadget['serial_number']) ?></td>
          <td><?= htmlspecialchars($gadget['specifications']) ?></td>
          <td><?= number_format($gadget['price'], 2) ?></td>
          <td><?= $gadget['quantity'] ?></td>
          <td><?= $gadget['added_at'] ?></td>
          <td>
            <button 
              class="btn btn-sm btn-primary edit-gadget-btn" 
              data-bs-toggle="modal" 
              data-bs-target="#editGadgetModal"
              data-gadget='<?= json_encode($gadget) ?>'
            >
              <i class="bi bi-pencil-square"></i>
            </button>
            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure to delete this gadget?');">
              <input type="hidden" name="gadget_id" value="<?= $gadget['id'] ?>">
              <button type="submit" name="delete_gadget" class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <?php 
      }
      $stmt->close();
  }
  ?>

</div>

<!-- Edit Phone Modal -->
<div class="modal fade" id="editPhoneModal" tabindex="-1" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" id="editPhoneForm">
      <input type="hidden" name="phone_id" id="phone_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPhoneModalLabel">Edit Phone</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">

          <div class="col-md-6">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" id="brand" name="brand" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="model" class="form-label">Model</label>
            <input type="text" id="model" name="model" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="imei" class="form-label">IMEI</label>
            <input type="text" id="imei" name="imei" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="storage" class="form-label">Storage</label>
            <input type="text" id="storage" name="storage" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="color" class="form-label">Color</label>
            <input type="text" id="color" name="color" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="condition" class="form-label">Condition</label>
            <select id="condition" name="condition" class="form-select" required>
              <option value="New">New</option>
              <option value="Refurbished">Refurbished</option>
              <option value="Used">Used</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="price" class="form-label">Price (UGX)</label>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" min="0" id="quantity" name="quantity" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
              <option value="Available">Available</option>
              <option value="Sold">Sold</option>
              <option value="Returned">Returned</option>
              <option value="Swapped">Swapped</option>
              <option value="Damaged">Damaged</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" name="update_phone" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Gadget Modal -->
<div class="modal fade" id="editGadgetModal" tabindex="-1" aria-labelledby="editGadgetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" id="editGadgetForm">
      <input type="hidden" name="gadget_id" id="gadget_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editGadgetModalLabel">Edit Gadget</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">

          <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="model_gadget" class="form-label">Model</label>
            <input type="text" id="model_gadget" name="model" class="form-control">
          </div>

          <div class="col-md-6">
            <label for="serial_number" class="form-label">Serial Number</label>
            <input type="text" id="serial_number" name="serial_number" class="form-control">
          </div>

          <div class="col-md-6">
            <label for="specifications" class="form-label">Specifications</label>
            <textarea id="specifications" name="specifications" class="form-control" rows="2"></textarea>
          </div>

          <div class="col-md-6">
            <label for="price_gadget" class="form-label">Price (UGX)</label>
            <input type="number" step="0.01" min="0" id="price_gadget" name="price" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="quantity_gadget" class="form-label">Quantity</label>
            <input type="number" min="0" id="quantity_gadget" name="quantity" class="form-control" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" name="update_gadget" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle filling edit phone modal
  const editPhoneModal = document.getElementById('editPhoneModal');
  editPhoneModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const phone = JSON.parse(button.getAttribute('data-phone'));

    document.getElementById('phone_id').value = phone.id;
    document.getElementById('brand').value = phone.brand;
    document.getElementById('model').value = phone.model;
    document.getElementById('imei').value = phone.imei;
    document.getElementById('storage').value = phone.storage;
    document.getElementById('color').value = phone.color;
    document.getElementById('condition').value = phone.condition;
    document.getElementById('price').value = phone.price;
    document.getElementById('quantity').value = phone.quantity;
    document.getElementById('status').value = phone.status;
  });

  // Handle filling edit gadget modal
  const editGadgetModal = document.getElementById('editGadgetModal');
  editGadgetModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const gadget = JSON.parse(button.getAttribute('data-gadget'));

    document.getElementById('gadget_id').value = gadget.id;
    document.getElementById('name').value = gadget.name;
    document.getElementById('model_gadget').value = gadget.model;
    document.getElementById('serial_number').value = gadget.serial_number;
    document.getElementById('specifications').value = gadget.specifications;
    document.getElementById('price_gadget').value = gadget.price;
    document.getElementById('quantity_gadget').value = gadget.quantity;
  });
});
</script>

<?php require '../includes/footer.php'; ?>
