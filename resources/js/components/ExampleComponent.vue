<template>
    <div class="q-pa-md">
        <q-btn label="Verificar Estado" @click="checkStatus" color="primary" />
        <p v-if="status !== null">Estado del servidor: {{ status }}</p>
    </div>
</template>

<script>
console.log('event 1');

const response = await axios.get("http://localhost/srh/public/api/status"); // ✅ Usa ruta relativa
console.log(response);
//this.status = response.data.status ? "Activo ✅" : "Inactivo ❌";

import axios from "axios";

export default {
    data() {
        return {
            status: null,
        };
    },
    methods: {
        async checkStatus() {
            try {
                console.log('event 32');
                const response = await axios.get("http://localhost/srh/public/api/status"); // ✅ Usa ruta relativa
                this.status = response.data.status ? "Activo ✅" : "Inactivo ❌";
            } catch (error) {
                this.status = "Error en la conexión ❌";
            }
        },
    },
};
</script>