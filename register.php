<?php
// register.php
require_once 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            if ($role === 'Donor') {
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
                $gender = $_POST['gender'];
                $blood_group = $_POST['blood_group'];
                $district = filter_input(INPUT_POST, 'district', FILTER_SANITIZE_STRING);

                $stmt = $pdo->prepare("INSERT INTO DONOR (Name, Age, Gender, Blood_Group, Phone, Email, District, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $age, $gender, $blood_group, $phone, $email, $district, $hashed_password]);

            } elseif ($role === 'Recipient') {
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $blood_group = $_POST['blood_group'];
                $district = filter_input(INPUT_POST, 'district', FILTER_SANITIZE_STRING);
                $emergency = isset($_POST['emergency']) ? 'Yes' : 'No';

                $stmt = $pdo->prepare("INSERT INTO RECIPIENT (Name, Blood_Group, Phone, Email, District, Emergency_Flag, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $blood_group, $phone, $email, $district, $emergency, $hashed_password]);

            } elseif ($role === 'Hospital') {
                $hospital_name = filter_input(INPUT_POST, 'hospital_name', FILTER_SANITIZE_STRING);
                $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);

                $stmt = $pdo->prepare("INSERT INTO HOSPITAL (Hospital_Name, Location, Contact_Number, Email, Password) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$hospital_name, $location, $phone, $email, $hashed_password]);

            } else {
                $error = "Invalid role selected.";
            }

            if (empty($error)) {
                header("Location: index.php?success=registered");
                exit();
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "An account with this email already exists.";
            } else {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!-- Registration Page -->
<div style="max-width:900px; margin:0 auto; padding: 10px 0 40px;">

    <!-- Page Header -->
    <div style="text-align:center; margin-bottom:36px;">
        <div
            style="width:60px;height:60px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:18px;display:flex;align-items:center;justify-content:center;color:white;font-size:1.5rem;margin:0 auto 16px;box-shadow:var(--shadow-primary);">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h1 style="font-size:1.9rem; font-weight:800; color:var(--text-dark); margin-bottom:6px;">Create Your Account
        </h1>
        <p style="color:var(--text-muted); font-size:0.95rem;">Join the LifeDrop network and help save lives in Wayanad
        </p>
    </div>

    <!-- Role Selector Tabs -->
    <div
        style="display:flex; gap:0; border:2px solid var(--border); border-radius:var(--radius-md); overflow:hidden; margin-bottom:28px; background:var(--bg-page);">
        <button type="button" class="role-tab active" data-role="Donor" onclick="selectRole('Donor')">
            <i class="fa-solid fa-heart"></i> Blood Donor
        </button>
        <button type="button" class="role-tab" data-role="Recipient" onclick="selectRole('Recipient')">
            <i class="fa-solid fa-bed-pulse"></i> Patient / Recipient
        </button>
        <button type="button" class="role-tab" data-role="Hospital" onclick="selectRole('Hospital')">
            <i class="fa-solid fa-hospital"></i> Hospital / Blood Bank
        </button>
    </div>

    <style>
        .role-tab {
            flex: 1;
            padding: 14px 20px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .role-tab:hover {
            color: var(--primary);
            background: var(--primary-glow);
        }

        .role-tab.active {
            background: var(--primary);
            color: white;
        }
    </style>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Main Registration Form -->
    <form action="register.php" method="POST" id="registrationForm">
        <input type="hidden" name="role" id="roleInput" value="Donor">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

            <!-- Left Column: Account Info -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-lock"
                            style="color:var(--primary);margin-right:8px;"></i>Account Information</span>
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" required placeholder="your@email.com"
                        autocomplete="email">
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" class="form-control" required placeholder="+91 9xxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="6"
                        placeholder="Min. 6 characters">
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6"
                        placeholder="Repeat password">
                </div>
            </div>

            <!-- Right Column: Profile Details -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-id-card"
                            style="color:var(--primary);margin-right:8px;"></i>Profile Details</span>
                </div>

                <!-- Full Name (Donor / Recipient) -->
                <div class="form-group role-field-personal">
                    <label>Full Name *</label>
                    <input type="text" name="name" id="nameField" class="form-control" required
                        placeholder="Your full name">
                </div>

                <!-- Hospital Name -->
                <div class="form-group role-field-hospital" style="display:none;">
                    <label>Hospital / Institution Name *</label>
                    <input type="text" name="hospital_name" id="hospitalNameField" class="form-control"
                        placeholder="e.g. Wayanad General Hospital">
                </div>

                <!-- Age (Donor only) -->
                <div class="form-group role-field-donor">
                    <label>Age *</label>
                    <input type="number" name="age" id="ageField" class="form-control" min="18" max="65" required
                        placeholder="18–65 years">
                </div>

                <!-- Gender (Donor only) -->
                <div class="form-group role-field-donor">
                    <label>Gender *</label>
                    <select name="gender" id="genderField" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Blood Group (Donor / Recipient) -->
                <div class="form-group role-field-blood">
                    <label>Blood Group *</label>
                    <select name="blood_group" id="bloodField" class="form-control" required>
                        <option value="">Select Blood Group…</option>
                        <option>A+</option>
                        <option>A-</option>
                        <option>B+</option>
                        <option>B-</option>
                        <option>AB+</option>
                        <option>AB-</option>
                        <option>O+</option>
                        <option>O-</option>
                    </select>
                </div>

                <!-- District (Donor / Recipient) -->
                <div class="form-group role-field-district">
                    <label>District / City *</label>
                    <select name="district" id="districtField" class="form-control" required>
                        <option value="">Select District…</option>
                        <option value="Thiruvananthapuram">Thiruvananthapuram</option>
                        <option value="Kollam">Kollam</option>
                        <option value="Pathanamthitta">Pathanamthitta</option>
                        <option value="Alappuzha">Alappuzha</option>
                        <option value="Kottayam">Kottayam</option>
                        <option value="Idukki">Idukki</option>
                        <option value="Ernakulam">Ernakulam</option>
                        <option value="Thrissur">Thrissur</option>
                        <option value="Palakkad">Palakkad</option>
                        <option value="Malappuram">Malappuram</option>
                        <option value="Kozhikode">Kozhikode</option>
                        <option value="Wayanad">Wayanad</option>
                        <option value="Kannur">Kannur</option>
                        <option value="Kasaragod">Kasaragod</option>
                    </select>
                </div>

                <!-- Location (Hospital) -->
                <div class="form-group role-field-hospital" style="display:none;">
                    <label>Full Address *</label>
                    <textarea name="location" id="locationField" class="form-control"
                        placeholder="Full hospital address…"></textarea>
                </div>

                <!-- Emergency Flag (Recipient) -->
                <div class="form-group role-field-recipient" style="display:none;">
                    <label
                        style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; padding:14px; background:var(--accent-red-light); border-radius:var(--radius-sm); border:1.5px solid rgba(230,57,70,0.25);">
                        <input type="checkbox" name="emergency" value="Yes"
                            style="margin-top:3px; width:16px; height:16px; accent-color:var(--accent-red);">
                        <div>
                            <strong style="color:var(--accent-red);">Mark as Emergency Request</strong>
                            <p style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">Only use if you need
                                blood immediately. This will trigger priority alerts.</p>
                        </div>
                    </label>
                </div>
            </div>

        </div><!-- grid -->

        <!-- Submit -->
        <div style="margin-top:24px;">
            <button type="submit" class="btn btn-primary"
                style="width:100%; padding:15px; font-size:1rem; border-radius:var(--radius-md);">
                <i class="fa-solid fa-user-check"></i> Create My Account
            </button>
            <p style="text-align:center; margin-top:16px; font-size:0.88rem; color:var(--text-muted);">
                Already have an account? <a href="login.php" style="color:var(--primary); font-weight:600;">Sign in
                    here</a>
            </p>
            <p style="text-align:center; font-size:0.78rem; color:var(--text-muted); margin-top:8px;">
                <i class="fa-solid fa-shield-check" style="color:var(--primary);"></i>
                All accounts require admin approval before you can log in.
            </p>
        </div>

    </form>
</div>

<script>
    function selectRole(role) {
        document.getElementById('roleInput').value = role;

        // Update tab styles
        document.querySelectorAll('.role-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.role === role);
        });

        // Field groups
        const donorFields = document.querySelectorAll('.role-field-donor');
        const recipientFields = document.querySelectorAll('.role-field-recipient');
        const hospitalFields = document.querySelectorAll('.role-field-hospital');
        const personalFields = document.querySelectorAll('.role-field-personal');
        const bloodFields = document.querySelectorAll('.role-field-blood');
        const districtFields = document.querySelectorAll('.role-field-district');

        // Reset required
        ['nameField', 'hospitalNameField', 'ageField', 'genderField', 'bloodField', 'districtField', 'locationField'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.required = false;
        });

        // Hide all
        [donorFields, recipientFields, hospitalFields, personalFields, bloodFields, districtFields].forEach(group =>
            group.forEach(el => el.style.display = 'none')
        );

        // Show relevant
        if (role === 'Donor') {
            [donorFields, personalFields, bloodFields, districtFields].forEach(g => g.forEach(el => el.style.display = 'block'));
            ['nameField', 'ageField', 'genderField', 'bloodField', 'districtField'].forEach(id => { const el = document.getElementById(id); if (el) el.required = true; });
        } else if (role === 'Recipient') {
            [recipientFields, personalFields, bloodFields, districtFields].forEach(g => g.forEach(el => el.style.display = 'block'));
            ['nameField', 'bloodField', 'districtField'].forEach(id => { const el = document.getElementById(id); if (el) el.required = true; });
        } else if (role === 'Hospital') {
            hospitalFields.forEach(el => el.style.display = 'block');
            ['hospitalNameField', 'locationField'].forEach(id => { const el = document.getElementById(id); if (el) el.required = true; });
        }
    }

    document.addEventListener('DOMContentLoaded', () => selectRole('Donor'));
</script>

<?php require_once 'includes/footer.php'; ?>