<script>
import axios from "axios";
try {
    const response = await axios.get("http://localhost/srh/public/api/status");
    console.log(response.data.message);
    console.log(response.data.status);
} catch (error) {
    console.error(error);
}
</script>