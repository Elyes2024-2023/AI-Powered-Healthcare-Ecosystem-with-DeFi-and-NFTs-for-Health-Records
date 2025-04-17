// SPDX-License-Identifier: MIT
/**
 * Health Record NFT Smart Contract
 * 
 * @copyright ELYES 2024-2025
 * @author ELYES
 * @package Healthcare-Ecosystem
 */

pragma solidity ^0.8.19;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Counters.sol";

/**
 * @title HealthRecordNFT
 * @dev Implementation of the Health Record NFT contract
 * @author ELYES
 */
contract HealthRecordNFT is ERC721URIStorage, Ownable {
    using Counters for Counters.Counter;
    Counters.Counter private _tokenIds;

    // Mapping from token ID to approved healthcare providers
    mapping(uint256 => mapping(address => bool)) private _approvedProviders;

    // Events
    event HealthRecordCreated(uint256 indexed tokenId, address indexed patient);
    event ProviderApproved(uint256 indexed tokenId, address indexed provider);
    event ProviderRevoked(uint256 indexed tokenId, address indexed provider);

    constructor() ERC721("HealthRecord", "HREC") {}

    /**
     * @dev Creates a new health record NFT
     * @param patient Address of the patient
     * @param uri IPFS URI containing the encrypted health record data
     */
    function createHealthRecord(address patient, string memory uri) public returns (uint256) {
        _tokenIds.increment();
        uint256 newTokenId = _tokenIds.current();

        _safeMint(patient, newTokenId);
        _setTokenURI(newTokenId, uri);

        emit HealthRecordCreated(newTokenId, patient);
        return newTokenId;
    }

    /**
     * @dev Approves a healthcare provider to access the health record
     * @param tokenId The ID of the health record NFT
     * @param provider Address of the healthcare provider
     */
    function approveProvider(uint256 tokenId, address provider) public {
        require(_isApprovedOrOwner(_msgSender(), tokenId), "Caller is not owner nor approved");
        require(provider != address(0), "Invalid provider address");
        
        _approvedProviders[tokenId][provider] = true;
        emit ProviderApproved(tokenId, provider);
    }

    /**
     * @dev Revokes a healthcare provider's access to the health record
     * @param tokenId The ID of the health record NFT
     * @param provider Address of the healthcare provider
     */
    function revokeProvider(uint256 tokenId, address provider) public {
        require(_isApprovedOrOwner(_msgSender(), tokenId), "Caller is not owner nor approved");
        require(provider != address(0), "Invalid provider address");
        
        _approvedProviders[tokenId][provider] = false;
        emit ProviderRevoked(tokenId, provider);
    }

    /**
     * @dev Checks if a provider is approved to access a health record
     * @param tokenId The ID of the health record NFT
     * @param provider Address of the healthcare provider
     */
    function isProviderApproved(uint256 tokenId, address provider) public view returns (bool) {
        return _approvedProviders[tokenId][provider];
    }

    /**
     * @dev Updates the URI of an existing health record
     * @param tokenId The ID of the health record NFT
     * @param uri New IPFS URI containing the updated encrypted health record data
     */
    function updateHealthRecord(uint256 tokenId, string memory uri) public {
        require(_isApprovedOrOwner(_msgSender(), tokenId), "Caller is not owner nor approved");
        _setTokenURI(tokenId, uri);
    }
} 