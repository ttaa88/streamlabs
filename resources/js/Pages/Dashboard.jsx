import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import React from 'react';
import { Head } from '@inertiajs/react';
import { Button } from 'primereact/button';
import { Card } from 'primereact/card';
import { Column } from 'primereact/column';
import { DataTable } from 'primereact/datatable'
import { ListBox } from 'primereact/listbox';
import { router } from '@inertiajs/react'

export default function Dashboard({ auth, displayMessages, totalFollowers, totalRevenue, topMerchSales }) {
    const pageLeft = () => {
        if (displayMessages.prev_page_url) {
            router.visit(displayMessages.prev_page_url, { method: 'get' });
        }
    };

    const pageRight = () => {
        if (displayMessages.next_page_url) {
            router.visit(displayMessages.next_page_url, { method: 'get' });
        }
    };

    const paginatorLeft = <Button type="button" onClick={pageLeft} label='Next 100' />;
    const paginatorRight = <Button type="button" onClick={pageRight} label='Previous 100' />;

    const totalFollowersCard = (
        <div className="card">
            <Card style={{ maxWidth: "300px", height: "300px" }} title="Total amount of followers they have gained in the past 30 days">
                <p style={{ fontSize: 40 }} className="m-0">
                    {totalFollowers}
                </p>
            </Card>
        </div>
    );

    const totalRevenueCard = (
        <div className="card">
            <Card style={{ maxWidth: "300px", height: "300px" }} title="Total revenue they made in the past 30 days from Donations, Subscriptions & Merch sales">
                <p style={{ fontSize: 40 }} className="m-0">
                    {totalRevenue}
                </p>
            </Card>
        </div>
    );

    const topMerchSalesList = (
        <ListBox options={topMerchSales} className="w-full md:w-14rem" />
    )

    const messageTable = (
        <DataTable value={displayMessages.data} paginator rows={10} rowsPerPageOptions={[10, 20, 50, 100]} tableStyle={{ minWidth: '50rem' }}
            paginatorLeft={paginatorLeft} paginatorRight={paginatorRight} >
            <Column field="formattedMessage" header="Message" />
        </DataTable>
    )

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="card flex gap-4" style={{paddingBottom: "20px"}}>
                        {totalFollowersCard}
                        {totalRevenueCard}
                    </div>
                    <h4>
                        Top 3 items that did the best sales wise in the past 30 days
                    </h4>
                    {topMerchSalesList}
                    {messageTable}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
