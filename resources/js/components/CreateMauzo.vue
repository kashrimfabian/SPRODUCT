<template>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Add New Mauzo (Vue)</h4>

                <div class="mb-3">
                    <label for="tarehe" class="form-label">Date</label>
                    <input type="date" id="tarehe" class="form-control datepicker" required v-model="formData.tarehe" placeholder="select date">
                </div>

                <div class="mb-3">
                    <label for="alizeti_id" class="form-label">Select Batch Number</label>
                    <select id="alizeti_id" class="form-select" required v-model="formData.alizeti_id" @change="updateFields">
                        <option value="">-- Select Batch --</option>
                        <option v-for="batch in alizetiBatches" :key="batch.ali_id" :value="batch.ali_id"
                            :data-available-mafuta="batch.stock ? batch.stock.mafuta_masafi : 0"
                            :data-available-mashudu="batch.stock ? batch.stock.mashudu : 0">
                            {{ batch.batch_no }}
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="sale_type" class="form-label">Sales Type (Mafuta/Mashudu)</label>
                    <select id="sale_type" class="form-select" required v-model="formData.sale_type" @change="updateFields">
                        <option value="mafuta">mafuta</option>
                        <option value="mashudu">mashudu</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price_display" class="form-label">Price</label>
                    <input type="text" id="price_display" class="form-control" readonly :value="priceDisplay">
                    <input type="hidden" id="price" v-model="formData.price">
                </div>

                <div class="mb-3">
                    <label for="available_quantity" class="form-label">Available Quantity (Lts/Kgs)</label>
                    <input type="text" id="available_quantity" class="form-control" readonly :value="availableQuantity">
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity (Lts/Kgs)</label>
                    <input type="number" step="any" id="quantity" class="form-control" required v-model.number="formData.quantity" @input="calculateTotalPrice">
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label">Discount (TZS)</label>
                    <input type="number" id="discount" class="form-control" step="0.01" v-model.number="formData.discount" @input="calculateTotalPrice">
                </div>

                <div class="mb-3">
                    <label for="total_price_display" class="form-label">Total Price</label>
                    <div class="border p-2">
                        <span id="total_price_display">{{ totalPriceDisplay }}</span>
                    </div>
                    <input type="hidden" id="total_price" v-model="formData.total_price">
                </div>

                <div class="mb-3">
                    <label for="sells_type" class="form-label">Sales Category (Jumla/Rejareja)</label>
                    <select id="sells_type" class="form-select" required v-model="formData.sells_type">
                        <option value="jumla">jumla</option>
                        <option value="rejareja">rejareja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_way" class="form-label">Payment Way</label>
                    <select id="payment_way" class="form-select" required v-model="formData.payment_way">
                        <option value="cash">Cash</option>
                        <option value="Lipa_namba">Lipa Namba</option>
                    </select>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-success mt-3" @click="submitForm"><i class="fas fa-save"></i>Save</button>
                    <router-link :to="{ name: 'mauzo.index' }" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i>Back</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const formData = reactive({
    tarehe: '',
    alizeti_id: '',
    quantity: 0,
    payment_way: 'cash',
    discount: 0,
    sale_type: 'mafuta',
    price: 0,
    sells_type: 'jumla',
    total_price: 0,
});

const alizetiBatches = ref([]);
const priceDisplay = ref(0);
const availableQuantity = ref(0);
const totalPriceDisplay = ref('0.00');

onMounted(async () => {
    try {
        const response = await axios.get('/api/alizeti');
        alizetiBatches.value = response.data;
    } catch (error) {
        console.error('Error fetching alizeti batches:', error);
    }
});

const updateFields = async () => {
    if (formData.alizeti_id && formData.sale_type) {
        try {
            const response = await axios.get(`/api/get-price/${formData.alizeti_id}/${formData.sale_type}`);
            priceDisplay.value = response.data.price;
            formData.price = response.data.price;
        } catch (error) {
            console.error('Error fetching price:', error);
            priceDisplay.value = 'Error';
            formData.price = 0;
        }

        const selectedBatch = alizetiBatches.value.find(batch => batch.ali_id === formData.alizeti_id);
        if (selectedBatch) {
            availableQuantity.value = formData.sale_type === 'mafuta' ? (selectedBatch.stock ? selectedBatch.stock.mafuta_masafi : 0) : (selectedBatch.stock ? selectedBatch.stock.mashudu : 0);
        } else {
            availableQuantity.value = 0;
        }
    } else {
        priceDisplay.value = 0;
        formData.price = 0;
        availableQuantity.value = 0;
    }
    calculateTotalPrice();
};

const calculateTotalPrice = () => {
    const subtotal = formData.quantity * formData.price;
    const finalPrice = subtotal - formData.discount;
    totalPriceDisplay.value = finalPrice.toFixed(2);
    formData.total_price = parseFloat(finalPrice.toFixed(2));
};

const submitForm = async () => {
    try {
        await axios.post('/api/mauzo', formData);
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Mauzo added successfully.',
            timer: 2000,
            showConfirmButton: false
        });
        router.push({ name: 'mauzo.index' });
    } catch (error) {
        console.error('Error adding mauzo:', error.response ? error.response.data : error.message);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.response ? JSON.stringify(error.response.data) : error.message,
        });
    }
};
</script>

<style scoped>
/* Component-specific styles */
</style>