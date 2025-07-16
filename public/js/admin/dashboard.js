        // Transaction functions
        function showAddTransaction() {
            const transactionHtml = `
        <tr id="newTransactionRow">
            <td><input type="date" class="form-control" style="width: 140px;" required></td>
            <td><input type="text" class="form-control" placeholder="Keterangan" required></td>
            <td>
                <select class="form-control" style="width: 120px;" required>
                    <option value="iuran">Iuran</option>
                    <option value="donasi">Donasi</option>
                    <option value="bantuan">Bantuan</option>
                    <option value="lainnya_masuk">Lainnya (Masuk)</option>
                    <option value="keamanan">Keamanan</option>
                    <option value="infrastruktur">Infrastruktur</option>
                    <option value="acara">Acara</option>
                    <option value="operasional">Operasional</option>
                    <option value="lainnya_keluar">Lainnya (Keluar)</option>
                </select>
            </td>
            <td>
                <select class="form-control" style="width: 120px;" required>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </td>
            <td><input type="number" class="form-control" placeholder="Jumlah" required></td>
            <td>
                <select class="form-control" style="width: 120px;" required>
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="check">Cek</option>
                </select>
            </td>
            <td>
                <button class="btn btn-success btn-sm" onclick="saveTransaction()">Simpan</button>
                <button class="btn btn-danger btn-sm" onclick="cancelTransaction()">Batal</button>
            </td>
        </tr>
    `;

            const tableBody = document.getElementById('transactionTable');
            tableBody.insertAdjacentHTML('afterbegin', transactionHtml);
        }

        function saveTransaction() {
            const row = document.getElementById('newTransactionRow');
            const inputs = row.querySelectorAll('input, select');

            // Validate inputs
            let isValid = true;
            const data = {};

            inputs.forEach(input => {
                if (input.required && !input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });

            if (!isValid) {
                alert('Mohon lengkapi semua field yang wajib diisi');
                return;
            }

            // Collect data
            data.transaction_date = inputs[0].value;
            data.title = inputs[1].value;
            data.category = inputs[2].value;
            data.type = inputs[3].value;
            data.amount = inputs[4].value;
            data.payment_method = inputs[5].value;
            data.description = inputs[1].value; // Use title as description

            // Send AJAX request
            fetch('/admin/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage('Transaksi berhasil disimpan!');
                        cancelTransaction();

                        // Optionally refresh the transaction table
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert(data.message || 'Gagal menyimpan transaksi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan transaksi');
                });
        }

        function cancelTransaction() {
            const newRow = document.getElementById('newTransactionRow');
            if (newRow) {
                newRow.remove();
            }
        }

        function showTransactionDetail(transactionId) {
            fetch(`/admin/transactions/${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const transaction = data.transaction;
                        const detailHtml = `
                            <p><strong>Judul:</strong> ${transaction.title}</p>
                            <p><strong>Deskripsi:</strong> ${transaction.description || '-'}</p>
                            <p><strong>Kategori:</strong> ${transaction.category}</p>
                            <p><strong>Jenis:</strong> ${transaction.type}</p>
                            <p><strong>Jumlah:</strong> Rp ${transaction.amount.toLocaleString('id-ID')}</p>
                            <p><strong>Metode Pembayaran:</strong> ${transaction.payment_method}</p>
                            <p><strong>Tanggal:</strong> ${new Date(transaction.transaction_date).toLocaleDateString('id-ID')}</p>
                            
                            <p><strong>Dibuat oleh:</strong> ${transaction.user.name}</p>
                            <p><strong>Catatan:</strong> ${transaction.notes || '-'}</p>
                        `;
                        document.getElementById('transactionDetail').innerHTML = detailHtml;
                        document.getElementById('transactionModal').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail transaksi');
                });
        }

        function verifyTransaction(transactionId) {
            if (confirm('Apakah Anda yakin ingin memverifikasi transaksi ini?')) {
                fetch(`/admin/transactions/${transactionId}/verify`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessMessage('Transaksi berhasil diverifikasi!');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            alert(data.message || 'Gagal memverifikasi transaksi');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memverifikasi transaksi');
                    });
            }
        }

        function closeModal() {
            document.getElementById('transactionModal').style.display = 'none';
        }

        function showSuccessMessage(message) {
            // Create and show success notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success';
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        color: #155724;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    `;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Auto refresh untuk data realtime (optional)
        setInterval(function () {
            console.log('Refreshing admin dashboard data...');
            // Implementasi refresh data jika diperlukan
        }, 300000); // 5 minutes