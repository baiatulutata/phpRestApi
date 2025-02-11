<template>
    <div>
        <button @click="login">Login</button>
    <button @click="fetchUsers">Fetch Users</button>
<ul v-if="users.length">
    <li v-for="user in users" :key="user.id">{{ user.username }}</li>
</ul>
</div>
</template>

<script>
    import axios from 'axios';

    export default {
    data() {
    return {
    users: [],
    token: '',
};
},
    methods: {
    async login() {
    try {
    const response = await axios.post('http://your-api-url/login', {
    username: 'admin',
    password: 'password123',
});
    this.token = response.data.token;
    console.log('Login successful');
} catch (error) {
    console.error('Login failed', error);
}
},
    async fetchUsers() {
    try {
    const response = await axios.get('http://your-api-url/users/list', {
    headers: {
    Authorization: `Bearer ${this.token}`,
},
});
    this.users = response.data;
} catch (error) {
    console.error('Failed to fetch users', error);
}
},
},
};
</script>
