<?php
// koneksi ke database menggunakan mysqli procedural
$host = 'localhost';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
	die('koneksi gagal: '.mysqli_connect_error());
}

// buat database baru dengan nama kantor jika belum ada
$sql = 'CREATE DATABASE IF NOT EXISTS kantor';
if (mysqli_query($conn, $sql)) {
	// pilih database untuk digunakan
	if (!mysqli_select_db($conn,"kantor")) {
		die('tidak ada database dengan nama kantor');
	}
} else {
	die('database gagal dibuat: '.mysqli_error($conn));
}

// buat tabel baru dengan nama karyawan jika belum ada
$sql = "CREATE TABLE IF NOT EXISTS karyawan (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nama_depan VARCHAR(30) NOT NULL,
nama_belakang VARCHAR(30) NOT NULL,
jenis_kelamin ENUM('L','P') NOT NULL,
email VARCHAR(50),
jabatan VARCHAR(50) NOT NULL,
alamat TEXT NOT NULL,
mulai_bekerja DATE NOT NULL,
date TIMESTAMP
)";

if (!mysqli_query($conn, $sql)) {
	die('gagal membuat tabel: '.mysqli_error($conn));
}

/*
Yeay!, database sudah siap.

Lanjut.

Pengetahuan dasar:
Alur sebuah aplikasi pada umumnya adalah input/request > proses/olah data > output/tampilkan
*/

// request
if(isset($_GET['page'])) {

	$errors = array();

	// request page add
	if($_GET['page'] == 'add') { 

		// request post
		if (isset($_POST['submit'])) {

			// proses/olah data
			if (empty($_POST['nama_depan'])) {
				$errors[] = 'Nama depan harus di isi.'; 
			}
			if (empty($_POST['nama_belakang'])) {
				$errors[] = 'Nama belakang harus di isi.';
			}
			if (empty($_POST['jenis_kelamin'])) {
				$errors[] = 'Jenis kelamin harus di isi.';
			}
			if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Alamat email tidak valid.';
            }
			if (empty($_POST['jabatan'])) {
				$errors[] = 'Jabatan harus di isi.';
			}
			if (empty($_POST['alamat'])) {
				$errors[] = 'Alamat harus di isi.';
			}
			if (empty($_POST['mulai_bekerja'])) {
				$errors[] = 'Mulai bekerja harus di isi.';
			}

			if (empty($errors)) {
				// tambahkan data ke database

				// // cara penulisan pertama
				// $sql = "INSERT INTO karyawan (nama_depan,nama_belakang,jenis_kelamin,email,jabatan,alamat,mulai_bekerja) VALUES ('".$_POST['nama_depan']."','".$_POST['nama_belakang']."','".$_POST['jenis_kelamin']."','".$_POST['email']."','".$_POST['jabatan']."','".$_POST['alamat']."','".$_POST['mulai_bekerja']."')";

				// cara penulisan kedua
				$sql = "INSERT INTO karyawan (nama_depan,nama_belakang,jenis_kelamin,email,jabatan,alamat,mulai_bekerja) VALUES ('$_POST[nama_depan]','$_POST[nama_belakang]','$_POST[jenis_kelamin]','$_POST[email]','$_POST[jabatan]','$_POST[alamat]','$_POST[mulai_bekerja]')";

				if (mysqli_query($conn, $sql)) {
					$notifikasi = 'Karyawan berhasil di tambahkan.';
				} else {
					die('gagal menambahkan data: '.mysqli_error($conn));
				}
			}
		}
		?>

		<!-- output/tampilkan -->
		<style>
			.form-group {
				margin-bottom: 15px;
			}	
			.form-group label {
			    width: 150px;
			    display: inline-block;
			    vertical-align: top;
			}
		</style>
		<h1>Tambah Karyawan</h1>
		<p><a href="?page=list">Kembali</a></p>
		<?php
		if (isset($notifikasi)) {
			echo "<p><i>$notifikasi</i></p>";
		}
		?>
		<form action="?page=add" method="post">
			<div class="form-group">
				<label for="nama_depan">Nama depan</label>
				<input type="text" name="nama_depan" id="nama_depan" value="<?php echo ($errors) ? $_POST['nama_depan'] : '' ?>">
			</div>
			<div class="form-group">
				<label for="nama_belakang">Nama Belakang</label>
				<input type="text" name="nama_belakang" id="nama_belakang" value="<?php echo ($errors) ? $_POST['nama_belakang'] : '' ?>">
			</div>
			<div class="form-group">
				<label for="jenis_kelamin">Jenis Kelamin</label>
				<select name="jenis_kelamin" id="jenis_kelamin">
					<option value="">Pilih jenis kelamin</option>
					<option value="L" <?php cek_selected($errors,'jenis_kelamin','L'); ?>>Laki-laki</option>
					<option value="P" <?php cek_selected($errors,'jenis_kelamin','P'); ?>>Perempuan</option>
				</select>
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" value="<?php echo ($errors) ? $_POST['email'] : '' ?>">
			</div>
			<div class="form-group">
				<label for="jabatan">Jabatan</label>
				<select name="jabatan" id="jabatan">
					<option value="">Pilih jabatan</option>
					<option value="owner" <?php cek_selected($errors,'jabatan','owner'); ?>>Owner</option>
					<option value="sekretaris" <?php cek_selected($errors,'jabatan','sekretaris'); ?>>Sekretaris</option>
					<option value="adm" <?php cek_selected($errors,'jabatan','adm'); ?>>Administrasi</option>
					<option value="cs" <?php cek_selected($errors,'jabatan','cs'); ?>>Customer Service</option>
					<option value="security" <?php cek_selected($errors,'jabatan','security'); ?>>Security</option>
					<option value="ob" <?php cek_selected($errors,'jabatan','ob'); ?>>Office Boy</option>
				</select>
			</div>
			<div class="form-group">
				<label for="alamat">Alamat</label>
				<textarea name="alamat" id="alamat" rows="10" cols="25"><?php echo ($errors) ? $_POST['alamat'] : '' ?></textarea>
			</div>
			<div class="form-group">
				<label for="mulai_bekerja">Mulai Bekerja</label>
				<!-- HTML5 date input type, ada beberapa versi browser yang belum support. cek http://caniuse.com/#search=date -->
				<input type="date" name="mulai_bekerja" id="mulai_bekerja" value="<?php echo ($errors) ? $_POST['mulai_bekerja'] : '' ?>">
			</div>
			<div class="form-group">
				<label></label>
				<input type="submit" name="submit" value="Simpan">
			</div>
			<?php if ($errors) { ?>
					<h3>Error:</h3>
					<ul>
					<?php foreach ($errors as $value) { ?>
						<li><?php echo $value; ?></li>
					<?php } ?>
					</ul>
			<?php } ?>
		</form>
		<?php

	// request page list dan penanganan delete
	} elseif($_GET['page'] == 'list') {

		// start penanganan delete karyawan
		if (isset($_GET['delete']) && !empty($_GET['delete'])) {
			
			$sql = "DELETE FROM karyawan WHERE id=$_GET[delete]";
			mysqli_query($conn, $sql);

			if (mysqli_affected_rows($conn) == 1) {
			    $notifikasi = 'Karyawan berhasil dihapus.';
			} else {
			    $notifikasi = 'Karyawan gagal dihapus.';
			}
		}
		// end penanganan delete karyawan

		// ambil data karyawan dari database
		$sql = 'SELECT * FROM karyawan';
		$query = mysqli_query($conn,$sql);
		$data = mysqli_fetch_all($query,MYSQLI_ASSOC);
		?>

		<!-- output/tampilkan -->
		<style>
			table, th, td {
			    border: 1px solid black;
			    border-collapse: collapse;
			}
		</style>
		<h1>Management Karyawan</h1>
		<p><a href="?page=add">Tambah Karyawan</a></p>
		<?php
		if (isset($notifikasi)) {
			echo "<p><i>$notifikasi</i></p>";
		}
		?>
		<table>
			<thead>
				<tr>
					<th>No</th>
					<th>Nama</th>
					<th>Jenis Kelamin</th>
					<th>Email</th>
					<th>Jabatan</th>
					<th>Alamat</th>
					<th>Mulai Bekerja</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($data) {
				foreach ($data as $k => $v) { ?>
					<tr>
						<td><?php echo $k+1; ?></td>
						<td><?php echo $v['nama_depan'].' '.$v['nama_belakang']; ?></td>
						<td><?php echo $v['jenis_kelamin']; ?></td>
						<td><?php echo $v['email']; ?></td>
						<td><?php echo $v['jabatan']; ?></td>
						<td><a href="https://www.google.com/maps?q=<?php echo $v['alamat'] ?>"><?php echo $v['alamat']; ?></a></td>
						<td><?php echo $v['mulai_bekerja']; ?></td>
						<td>
							<a href="?page=show&id=<?php echo $v['id']; ?>">Lihat</a>
							<a href="?page=edit&id=<?php echo $v['id']; ?>">Edit</a>
							<a href="?page=list&delete=<?php echo $v['id']; ?>">Delete</a>
						</td>
					</tr>
					<?php
				}
			} else { ?>
				<tr>
					<td colspan="8">Tidak ada karyawan.</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php

	// request page show
	} elseif($_GET['page'] == 'show' && !empty($_GET['id'])) {
		
		// ambil data karyawan dari database
		$sql = "SELECT * FROM karyawan WHERE id=$_GET[id]";
		$query = mysqli_query($conn, $sql);
		$data = mysqli_fetch_assoc($query);

		if (!$data) {
			die('Karyawan tidak ditemukan');
		}
		?>

		<!-- output/tampilkan -->
		<style>
			table, th, td {
			    border: 1px solid black;
			    border-collapse: collapse;
			}
		</style>
		<h1>Show <?php echo $data['nama_depan'].' '.$data['nama_belakang']; ?></h1>
		<p><a href="?page=list">Kembali</a></p>
		<table>
			<tbody>
				<tr>
					<td>Nama</td>
					<td><?php echo $data['nama_depan'].' '.$data['nama_belakang']; ?></td>
				</tr>
				<tr>
					<td>Jenis Kelamin</td>
					<td><?php echo $data['jenis_kelamin']; ?></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><?php echo $data['email']; ?></td>
				</tr>
				<tr>
					<td>Jabatan</td>
					<td><?php echo $data['jabatan']; ?></td>
				</tr>
				<tr>
					<td>Alamat</td>
					<td><a href="https://www.google.com/maps?q=<?php echo $data['alamat'] ?>"><?php echo $data['alamat']; ?></a></td>
				</tr>
				<tr>
					<td>Mulai Bekerja</td>
					<td><?php echo $data['mulai_bekerja']; ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	
	// request page edit
	} elseif($_GET['page'] == 'edit' && !empty($_GET['id'])) {

		// ambil data karyawan dari database
		$sql = "SELECT * FROM karyawan WHERE id=$_GET[id]";
		$query = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($query);

		if(!$data) {
			die('Karyawan tidak ditemukan');
		}

		// request post
		if (isset($_POST['submit'])) {

			// proses/olah data
			if (empty($_POST['nama_depan'])) {
				$errors[] = 'Nama depan harus di isi.'; 
			}
			if (empty($_POST['nama_belakang'])) {
				$errors[] = 'Nama belakang harus di isi.';
			}
			if (empty($_POST['jenis_kelamin'])) {
				$errors[] = 'Jenis kelamin harus di isi.';
			}
			if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Alamat email tidak valid.';
            }
			if (empty($_POST['jabatan'])) {
				$errors[] = 'Jabatan harus di isi.';
			}
			if (empty($_POST['alamat'])) {
				$errors[] = 'Alamat harus di isi.';
			}
			if (empty($_POST['mulai_bekerja'])) {
				$errors[] = 'Mulai bekerja harus di isi.';
			}

			if (empty($errors)) {
				// update data karyawan ke database
				$sql = "UPDATE karyawan SET nama_depan='$_POST[nama_depan]',nama_belakang='$_POST[nama_belakang]',jenis_kelamin='$_POST[jenis_kelamin]',email='$_POST[email]',jabatan='$_POST[jabatan]',alamat='$_POST[alamat]',mulai_bekerja='$_POST[mulai_bekerja]' WHERE id=$_GET[id]";

				if (mysqli_query($conn, $sql)) {
					$notifikasi = 'Karyawan berhasil di edit.';
					
					// ambil data karyawan dari database
					$sql = "SELECT * FROM karyawan WHERE id=$_GET[id]";
					$query = mysqli_query($conn,$sql);
					$data = mysqli_fetch_assoc($query);

					if(!$data) {
						die('Karyawan tidak ditemukan');
					}

				} else {
					die('gagal update data: '.mysqli_error($conn));
				}
			}
		}
		?>

		<!-- output/tampilkan -->
		<style>
			.form-group {
				margin-bottom: 15px;
			}	
			.form-group label {
			    width: 150px;
			    display: inline-block;
			    vertical-align: top;
			}
		</style>
		<h1>Edit Karyawan</h1>
		<p><a href="?page=list">Kembali</a></p>
		<?php
		if (isset($notifikasi)) {
			echo "<p><i>$notifikasi</i></p>";
		}
		?>
		<form action="?page=edit&id=<?php echo $_GET['id']; ?>" method="post">
			<div class="form-group">
				<label for="nama_depan">Nama depan</label>
				<input type="text" name="nama_depan" id="nama_depan" value="<?php echo ($errors) ? $_POST['nama_depan'] : $data['nama_depan'] ?>">
			</div>
			<div class="form-group">
				<label for="nama_belakang">Nama Belakang</label>
				<input type="text" name="nama_belakang" id="nama_belakang" value="<?php echo ($errors) ? $_POST['nama_belakang'] : $data['nama_belakang'] ?>">
			</div>
			<div class="form-group">
				<label for="jenis_kelamin">Jenis Kelamin</label>
				<select name="jenis_kelamin" id="jenis_kelamin">
					<option value="">Pilih jenis kelamin</option>
					<option value="L" <?php cek_selected($errors,'jenis_kelamin','L',$data); ?>>Laki-laki</option>
					<option value="P" <?php cek_selected($errors,'jenis_kelamin','P',$data); ?>>Perempuan</option>
				</select>
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" value="<?php echo ($errors) ? $_POST['email'] : $data['email'] ?>">
			</div>
			<div class="form-group">
				<label for="jabatan">Jabatan</label>
				<select name="jabatan" id="jabatan">
					<option value="">Pilih jabatan</option>
					<option value="owner" <?php cek_selected($errors,'jabatan','owner',$data); ?>>Owner</option>
					<option value="sekretaris" <?php cek_selected($errors,'jabatan','sekretaris',$data); ?>>Sekretaris</option>
					<option value="adm" <?php cek_selected($errors,'jabatan','adm',$data); ?>>Administrasi</option>
					<option value="cs" <?php cek_selected($errors,'jabatan','cs',$data); ?>>Customer Service</option>
					<option value="security" <?php cek_selected($errors,'jabatan','security',$data); ?>>Security</option>
					<option value="ob" <?php cek_selected($errors,'jabatan','ob',$data); ?>>Office Boy</option>
				</select>
			</div>
			<div class="form-group">
				<label for="alamat">Alamat</label>
				<textarea name="alamat" id="alamat" rows="10" cols="25"><?php echo ($errors) ? $_POST['alamat'] : $data['alamat'] ?></textarea>
			</div>
			<div class="form-group">
				<label for="mulai_bekerja">Mulai Bekerja</label>
				<!-- HTML5 date input type, ada beberapa versi browser yang belum support. cek http://caniuse.com/#search=date -->
				<input type="date" name="mulai_bekerja" id="mulai_bekerja" value="<?php echo ($errors) ? $_POST['mulai_bekerja'] : $data['mulai_bekerja'] ?>">
			</div>
			<div class="form-group">
				<label></label>
				<input type="submit" name="submit" value="Simpan">
			</div>
			<?php if ($errors) { ?>
					<h3>Error:</h3>
					<ul>
						<?php foreach ($errors as $value) { ?>
							<li><?php echo $value; ?></li>
						<?php } ?>
					</ul>
			<?php } ?>
		</form>
		<?php
	
	// notfound
	} else {

		die('page not found.');

	}

} else {

	die('welcome, please go to <a href="?page=list">admin area</a>.');

}

/* Helper */
function cek_selected($errors, $name, $value, $data = '') {
	/*
	jika ada errors dan post_select_(name) sama dengan select_option_(value) maka selected
	*/
	if (($errors) && $_POST[$name] == $value) {
		echo 'selected';
	}

	/*
	Jika tidak ada errors dan ada (data)_dari_database dan data_select_(name) sama dengan select_option_(value) maka selected
	*/
	if ((!$errors) && ($data) && $data[$name] == $value) {
		echo 'selected';
	}
}